@extends('core::admin.template')

@section('title', ucfirst($action).' Carousel')

@section('css')
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/ckeditor/ckeditor.js') }}
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Carousel</h1>
	@if ($action == 'edit')
		@if (!$carousel->deleted_at)
			{{ Form::open(array('role'=>'form',
								'url'=>'admin/carousels/delete/'.$carousel->id,
								'style'=>'margin-bottom:15px;')) }}
				<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
			{{ Form::close() }}
		@else
			{{ Form::open(array('role'=>'form',
								'url'=>'admin/carousels/hard-delete/'.$carousel->id,
								'class'=>'deleteForm',
								'data-confirm'=>'Delete this carousel forever?  This action cannot be undone!')) }}
				<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
			{{ Form::close() }}
			<a href="{{ url('admin/carousels/restore/'.$carousel->id) }}" class="btn btn-sm btn-success">Restore</a>
		@endif
	@endif

	@if ($action == 'edit')
		{{ Form::model($carousel) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form', 'method'=>'post')) }}
	@endif

	<div class="row">
		<div class="col-md-9">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>
							{{ Form::label('name', 'Name') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
							</div>
						</td>
					</tr>
	@if ($action == 'edit')
					<tr>
						<td>
							<b>Slides</b>
						</td>
						<td>
							<div class="slides">
								@foreach ($carousel->slides as $slide)
								<div class="slide">
									<p>
										{{ Form::text('slideNames['.$slide->id.']', $slide->name, array('class'=>'form-control', 'placeholder'=>'Name')) }}
									</p>
									<p>
										{{ Form::textarea('slideContents['.$slide->id.']', $slide->html, array('class'=>'ckeditor')) }}
									</p>
									<p class="text-left">
										<a type="button" class="btn btn-danger btn-sm delete-slide" href="{{ url('admin/carousels/delete-slide/' . $carousel->id . '/' . $slide->id )  }}">Delete Slide</a>
									</p>
								</div>
								@endforeach
							</div>
							<br />
							<div>
								<a class="btn btn-default btn-md" href="{{ url('admin/carousels/add-slide/' . $carousel->id ) }}">
									<span class="glyphicon glyphicon-plus"></span>
									Add Slide
								</a>
							</div>
						</td>
					</tr>
	@endif
				</tbody>
			</table>
		</div>{{-- Left Column --}}
	</div>{{-- Row --}}
	<div class="text-right pad">
		<input type="submit" class="btn btn-primary" value="Save" />
	</div>
	{{ Form::close() }}
@stop