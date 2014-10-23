@if (isset($items) && is_array($items))

<div class="btn-group btn-group-xs">
  @foreach($items as $key => $item)
    @if ($key !== 'group')
      <a class="btn btn-default" href="{{$item['route']}}"><i class="fa fa-{{$item['icon']}} fa-fw"></i></a>
    @else

        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
          <span class="fa fa-caret-down"></span></a>
          <ul class="dropdown-menu">
          
            @foreach($item as $keyg => $itemg)
              <li><a href="{{$itemg['route']}}"><i class="fa fa-{{$itemg['icon']}} fa-fw"></i> {{$keyg}}</a></li>
            @endforeach
          
          </ul>  
        </div>

    @endif

  @endforeach
  </div>
@endif