@extends('core::admin.template')

@section('title', 'Carousels')

@section('js')
@stop

@section('content')
	<div class="row pad">
		<div class="col-sm-8 pad">
			<h1>Carousels</h1>
			<a class="btn btn-sm btn-primary" href="{{ url('admin/carousels/add') }}">
				<span class="glyphicon glyphicon-plus"></span>
				Add
			</a>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
	<div class="row">
		<div class="col-sm-5">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:80px;"></th>
						<th style="width:80px;">ID</th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody>
				@if(count($carousels))
					@foreach($carousels as $carousel)
					<tr>
						<td>
							<a href="{{ url('admin/carousels/edit/' . $carousel->id) }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-edit"></span>
							</a>
							<a href="{{ url('admin/carousels/' . $carousel->id . '/slides') }}" class="btn btn-xs btn-default">
								<span class="glyphicon glyphicon-th-list"></span>
							</a>
							
						</td>
						<td>{{ $carousel->id }}</td>
						<td>{{ $carousel->name }}</td>
					</tr>
					@endforeach
				@else 
					<tr>
						<td colspan="5" style="padding:30px;text-align:center;">
							No Carousels Found
						</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
	<div class="row text-center">
		{{ $links }}
	</div>
@stop