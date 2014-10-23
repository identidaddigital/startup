<h1>
    <div class="col-lg-6">
        {{{$title}}}
    </div>
    <div class="col-lg-6">
        <div class="pull-right">
            <div class="btn-group">
                <a href="{{ $actions['create'] }}" class="btn btn-success btn-sm">Create</a>
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown">
                    Export <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ $actions['export_xls'] }}" target="_blank">XLS</a></li>
                    <li><a href="{{ $actions['export_pdf'] }}" target="_blank">PDF</a></li>
                  </ul>
                </div>
            </div>
        
        <!-- Single button -->
        </div>

    </div>
</h1>