@foreach($items as $item)
  @if (has_Permissions((array_key_exists('perms',$item) && is_array($item['perms'])) ? $item['perms'] : array()))
  <li>
      
        <a @if(array_key_exists('sub',$item)) href="#" @else href="{{ $item['url'] }}" @endif>
          @if(array_key_exists('icon',$item)) <i class="fa fa-{{ $item['icon'] }} fa-fw"></i> @endif
          {{ $item['title'] }} 
          @if(array_key_exists('sub',$item)) <span class="fa arrow"></span> @endif
      </a>
        @if(array_key_exists('sub',$item))
          <ul @if(array_key_exists('level',$item) && $item['level'] == 2) class="nav nav-third-level" @else class="nav nav-second-level" @endif >
              @include('admin.partials.menu-items', array('items' => $item['sub']))
          </ul> 
        @endif
  </li>
  @endif
@endforeach