<?php namespace Angel\Carousels;

use Angel\Core\AdminCrudController;

use Redirect, App, Input;

class AdminCarouselController extends AdminCrudController {

	protected $Model	= 'Carousel';
	protected $uri		= 'carousels';
	protected $plural	= 'carousels';
	protected $singular	= 'carousel';
	protected $package	= 'carousels';
	protected $slug     = 'name';

	public static function columns()
	{
		return array(
			'name',
			'transition_style',
			'auto_play'
		);
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required'
		);
	}

	public function after_save($carousel, &$changes = array())
	{
		$CarouselSlide = App::make('CarouselSlide');
		$slides        = Input::get('slides');

		if ($slides) {
			foreach ($slides as $slide_id => $html) {
				$slide = $CarouselSlide::where('id', $slide_id)->where('carousel_id', $carousel->id)->first();
				if (!$slide) {
					$slide = new $CarouselSlide;
					$slide->carousel_id = $carousel->id;
				}
				$slide->html = $html;
				$slide->save();
			}
		}
	}

	public function delete_slide($id)
	{
		$CarouselSlide = App::make('CarouselSlide');
		$slide = $CarouselSlide::where('id', $id)->firstOrFail();
		$carousel_id = $slide->carousel_id;
		$slide->delete();

		return Redirect::to('admin/carousels/edit/'.$carousel_id)->with('success', '
			<p>Slide successfully deleted.</p>
		');
	}

}