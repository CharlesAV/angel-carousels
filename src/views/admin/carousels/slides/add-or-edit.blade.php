@extends('core::admin.template')

@section('title', ucfirst($action).' Slide')

@section('css')
@stop

@section('js')
	{{ HTML::script('packages/angel/core/js/ckeditor/ckeditor.js') }}
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Slide</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>admin_uri('carousels/'.$slide->carousel_id.'/slides/delete/'.$slide->id),
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this item forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete" />
		{{ Form::close() }}
	@endif

	@if ($action == 'edit')
		{{ Form::model($slide, array('role'=>'form')) }}
	@elseif ($action == 'add')
		{{ Form::open(array('role'=>'form')) }}
	@endif

	<div class="row">
		<div class="col-md-9">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>
							<span class="required">*</span>
							{{ Form::label('name', 'Name') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('name', null, array('class'=>'form-control', 'required')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('html', 'Text') }}
						</td>
						<td>
							{{ Form::textarea('html',null,array('class' => 'ckeditor')) }}
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('image', 'Image') }}
						</td>
						<td>
							{{ Form::text('image', NULL, array('class'=>'form-control','style' => "float:left;width:300px;")) }}
							<button type="button" class="btn btn-default imageBrowse" style="float:left">Browse...</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>{{-- Row --}}
	<div class="text-right pad">
		<input type="submit" class="btn btn-primary" value="Save" />
	</div>
	{{ Form::close() }}
@stop