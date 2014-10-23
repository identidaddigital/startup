@extends('admin.template')
@section('title_page')
{{ View::make('admin.partials.header-datatable', $header) }}
@stop
@section('head')
@stop
@section('content')


		{{ $filter }}
		<div class="table-responsive">
		{{ $grid }}
		</div>
	
		<script type="text/javascript">
		$(function(){
			$('#chk-all').click(function(){
				if ($(this).hasClass('btnsuccess')){
					$('.btn-chk-id').removeClass('btnsuccess').find('.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
					$(this).removeClass('btnsuccess').find('.fa').removeClass('fa-check-square-o').addClass('fa-square-o');					
				}else{
					$('.btn-chk-id').addClass('btnsuccess').find('.fa').removeClass('fa-square-o').addClass('fa-check-square-o');	
					$(this).addClass('btnsuccess').find('.fa').removeClass('fa-square-o').addClass('fa-check-square-o');	
				}	
			});
			$('.btn-chk-id').click(function(){
				if ($(this).hasClass('btnsuccess')){
					$(this).removeClass('btnsuccess').find('.fa').removeClass('fa-check-square-o').addClass('fa-square-o');	
				}else{
					$(this).addClass('btnsuccess').find('.fa').removeClass('fa-square-o').addClass('fa-check-square-o');	
					$(this).find('.fa').removeClass('fa-square-o').addClass('fa-check-square-o');	
				}
				
			});

		});
		</script>

@stop
