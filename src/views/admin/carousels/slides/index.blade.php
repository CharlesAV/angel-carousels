@extends('core::admin.template')

@section('title', 'Slides')

@section('js')
	{{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
	<script>
		$(function() {
			$("tbody").sortable(sortObj);
		});
	</script>
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Slides</h1>
			<a class="btn btn-sm btn-primary" href="{{ admin_url('carousels/'.$carousel->id.'/slides/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-9">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:100px;"></th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody data-url="carousels/{{ $carousel->id }}/slides/order">
				@if(count($slides))
					@foreach ($slides as $slide)
						<tr data-id="{{ $slide->id }}">
							<td>
								<input type="hidden" class="orderInput" value="{{ $slide->order }}" />
								<a href="{{ $slide->link_edit() }}" class="btn btn-xs btn-default">
									<span class="glyphicon glyphicon-edit"></span>
								</a>
								<button type="button" class="btn btn-xs btn-default handle">
									<span class="glyphicon glyphicon-resize-vertical"></span>
								</button>

							</td>
							<td>{{ $slide->name }}</td>
						</tr>
					@endforeach
				@else 
					<tr>
						<td colspan="5" align="center" style="padding:30px;">
							No Slides Found.
						</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
@stop