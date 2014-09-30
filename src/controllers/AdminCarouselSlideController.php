<?php namespace Angel\Carousels;

use Angel\Core\AngelController;
use App, Input, View, Config, Validator, Redirect, Auth;

class AdminCarouselSlideController extends AngelController {
	protected $log_changes = true;
	protected $searchable = array(
		'name'
	);
	public $reorderable = true;
	
	// Columns to update on edit/add
	protected static function columns()
	{
		$columns = array(
			'name',
			'html',
			'image'
		);
		return $columns;
	}

	public function index($carousel)
	{
		$Carousel   = App::make('Carousel');
		$this->data['carousel'] = $Carousel->find($carousel);
		
		$CarouselSlide   = App::make('CarouselSlide');
		$slides = $CarouselSlide->withTrashed()->where('carousel_id','=',$carousel);

		if (isset($this->searchable) && count($this->searchable)) {
			$search = Input::get('search') ? urldecode(Input::get('search')) : null;
			$this->data['search'] = $search;

			if ($search) {
				$terms = explode(' ', $search);
				$slides = $slides->where(function($query) use ($terms) {
					foreach ($terms as $term) {
						$term = '%'.$term.'%';
						foreach ($this->searchable as $column) {
							$query->orWhere($column, 'like', $term);
						}
					}
				});
			}
		}

		$slides->orderBy('order','asc');
		$paginator = $slides->paginate();
		$this->data['slides'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make('carousels::admin.carousels.slides.index', $this->data);
	}
	
	public function add($carousel) {
		$Carousel   = App::make('Carousel');
		$this->data['carousel'] = $Carousel->find($carousel);
		
		$this->data['action'] = 'add';

		return View::make('carousels::admin.carousels.slides.add-or-edit', $this->data);
	}
	
	public function attempt_add($carousel)
	{
		$CarouselSlide = App::make('CarouselSlide');

		$errors = $this->validate();
		if (count($errors)) {
			return Redirect::to(admin_url('carousels/'.$carousel.'/slides/add'))->withInput()->withErrors($errors);
		}

		$object = new $CarouselSlide;
		foreach(static::columns() as $column) {
			$object->{$column} = Input::get($column);
		}
		if (isset($this->reorderable) && $this->reorderable) {
			$object->order = $CarouselSlide::count();
		}
		$object->carousel_id = $carousel;
		$object->save();
		
		if($object->file) $object->thumbs();

		if (method_exists($this, 'after_save')) $this->after_save($object);

		return Redirect::to(admin_url('carousels/'.$carousel.'/slides'))->with('success', '<p>Carousel slide successfully created.</p>');
	}
	
	public function edit($carousel,$id)
	{
		$Carousel   = App::make('Carousel');
		$this->data['carousel'] = $Carousel->find($carousel);
		
		$CarouselSlide = App::make('CarouselSlide');

		$slide = $CarouselSlide::withTrashed()->find($id);
		$this->data['slide'] = $slide;
		$this->data['action'] = 'edit';

		return View::make('carousels::admin.carousels.slides.add-or-edit', $this->data);
	}

	public function attempt_edit($carousel,$id)
	{
		$CarouselSlide  = App::make('CarouselSlide');
		$Change = App::make('Change');

		$errors = $this->validate($id);
		if (count($errors)) {
			return Redirect::to(admin_url('carousels/'.$carousel.'/slides/edit/'.$id))->withInput()->withErrors($errors);
		}

		$object  = $CarouselSlide::withTrashed()->findOrFail($id);
		$changes = array();
		$thumb = 0;
		
		foreach (static::columns() as $column) {
			$new_value = Input::get($column);

			if (isset($this->log_changes) && $this->log_changes && $object->{$column} != $new_value) {
				$changes[$column] = array(
					'old' => $object->{$column},
					'new' => $new_value
				);
			}
			if($column == "file" and $object->{$column} != $new_value) $thumb = 1;

			$object->{$column} = $new_value;
		}
		$object->save();
		
		if($thumb) $object->thumbs();

		if (method_exists($this, 'after_save')) $this->after_save($object, $changes);

		if (count($changes)) {
			$change = new $Change;
			$change->user_id = Auth::user()->id;
			$change->fmodel  = 'CarouselSlide';
			$change->fid     = $object->id;
			$change->changes = json_encode($changes);
			$change->save();
		}

		return Redirect::to(admin_url('carousels/'.$carousel.'/slides/edit/'.$object->id))->with('success', '
			<p>Carousel slide successfully updated.</p>
			<p><a href="' . admin_url('carousels/'.$carousel.'/slides') . '">Return to index</a></p>
		');
	}

	/**
	 * Validate all input when adding or editing.
	 *
	 * @param int $id - (Optional) ID of member beind edited
	 * @return array - An array of error messages to show why validation failed
	 */
	public function validate($id = null)
	{
		$validator = Validator::make(Input::all(), $this->validate_rules($id));
		$errors = ($validator->fails()) ? $validator->messages()->toArray() : array();
		return $errors;
	}

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}
	
	

	/**
	 * AJAX for reordering menu slides
	 */
	public function order()
	{
		$CarouselSlide   = App::make('CarouselSlide');
		$orders  = Input::get('orders');
		$objects = $CarouselSlide::whereIn('id', array_keys($orders))->get();

		foreach ($objects as $object) {
			$object->order = $orders[$object->id];
			$object->save();
		}

		return 1;
	}

	/**
	 * Called after delete/restore/etc. to ensure that the 'gap' in orders is filled in.
	 */
	public function reorder()
	{
		if (!isset($this->reorderable) || !$this->reorderable) return;
		$CarouselSlide = App::make('CarouselSlide');

		$objects = $CarouselSlide::orderBy('order')->get();

		$order = 0;
		foreach ($objects as $object) {
			$object->order = $order++;
			$object->save();
		}
	}

	public function delete($carousel, $id, $ajax = false)
	{
		$CarouselSlide = App::make('CarouselSlide');

		$object = $CarouselSlide::find($id);
		if (method_exists($object, 'pre_delete')) {
			$object->pre_delete();
		}
		$object->delete();

		$this->reorder();

		if ($ajax) return 1;

		return Redirect::to(admin_url('carousels/'.$carousel.'/slides'))->with('success', '
			<p>Carousel slide successfully deleted forever.</p>
		');
	}
}