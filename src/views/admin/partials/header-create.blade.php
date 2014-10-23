<h1>
    <div class="col-lg-6">
        {{{$title}}}
    </div>
    <div class="col-lg-6">
        <div class="pull-right">
            <a href="{{{ isset($actions['back']) ? $actions['back'] : URL::previous() }}}" class="btn btn-primary btn-sm">{{ trans('admin.header.back') }}</a>
        </div>
    </div>
</h1>
