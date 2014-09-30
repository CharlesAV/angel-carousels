@extends('core::admin.template')

@section('title', ucfirst($action).' Carousel')

@section('css')
@stop

@section('js')
@stop

@section('content')
	<h1>{{ ucfirst($action) }} Carousel</h1>
	@if ($action == 'edit')
		{{ Form::open(array('role'=>'form',
							'url'=>'admin/carousels/hard-delete/'.$carousel->id,
							'class'=>'deleteForm',
							'data-confirm'=>'Delete this carousel forever?  This action cannot be undone!')) }}
			<input type="submit" class="btn btn-sm btn-danger" value="Delete Forever" />
		{{ Form::close() }}
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
						<td style="width: 150px">
							{{ Form::label('name', 'Name') }}
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('name', null, array('class'=>'form-control', 'placeholder'=>'Name')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('auto_play', 'Transition Speed') }}
							<p>In milliseconds.<br> Set to 0 for no autotransition.</p>
						</td>
						<td>
							<div style="width:300px">
								{{ Form::text('auto_play', null, array('class'=>'form-control', 'placeholder'=>'Transition Speed')) }}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							{{ Form::label('transition_style', 'Transition Style') }}
							<p>Special transitions may not work in older browsers.</p>
						</td>
						<td>
							<div style="width:300px">
								<?php $Carousel = App::make('Carousel'); ?>
								{{ Form::select('transition_style', $Carousel::transition(), null, array('class'=>'form-control', 'placeholder'=>'Transition Style')) }}
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>{{-- Left Column --}}
	</div>{{-- Row --}}
	<div class="text-right pad">
		<input type="submit" class="btn btn-primary" value="Save" />
	</div>
	{{ Form::close() }}
@stop