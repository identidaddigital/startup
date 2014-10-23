<?php
use Carbon\Carbon;
class AdminController extends BaseController {
	
	protected $filter;
	protected $grid;
        
        public function hasAccess($array=array()){
            
            return Sentry::getUser()->hasAnyAccess($array);
            
        }
        
	protected function exportDataGrid($to='xls',$unset=null)
	{
	   		if ($unset !== NULL){
	   			if (is_array($unset))
	   			{
	   				foreach ($unset as $key => $value) {
	   					unset($this->grid->columns[$value]);
	   					unset($this->grid->headers[$key]);

	   				}
	   			}
	   			else
	   			{
	   				unset($unset);
	   			}
	   		}

	   		
	   		$name = Carbon::now()->format('Y_m_d_H_i_s');

			if ($to == 'xls')
			{
			   	$return = $this->grid->buildCSV(Request::segment(2).'_', $name);
			}
			else
			{
				$pdf = PDF::loadView('admin.exportpdf', array('grid'=>$this->grid));
				$return = $pdf->download(Request::segment(2)."_{$name}.pdf");
			}

			return $return;	   		

	}

	protected function buildDataGrid(){

		$this->grid->attributes(array("class"=>"table table-striped table-bordered"));
	   	$this->grid->add('{{$id}}', trans('admin.content.list_actions'))->style("width:150px")->cell( function ($value) {
            return View::make('admin.partials.row-action-datatable', array("items" => $this->getRowActions($value)))->render();
 		});

		$this->grid->row(function ($row) {
		   $row->cell('chk-all')->value = '<span style="border-radius: 20px;" class="btn btn-chk-id btn-xs"><i class="fa fa-square-o"></i></span>';
		});

		return array('grid'=>$this->grid,'filter'=>$this->filter);

	}

}