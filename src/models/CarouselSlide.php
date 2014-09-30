<?php namespace Angel\Carousels;

use Eloquent;

class CarouselSlide extends Eloquent {

	protected $table = 'carousels_slides';
	
	public function link_edit()
	{
		return admin_url('carousels/' . $this->carousel_id . '/slides/edit/' . $this->id);
	}
}