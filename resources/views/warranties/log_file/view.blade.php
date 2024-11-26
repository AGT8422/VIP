<div class="modal-dialog" role="document" style="width:80%">
    <div class="modal-content">
  
   
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'lang_v1.old_transaction' )</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            <h3>@lang("lang_v1.Source")</h3>
            <div class="main_source">
                <table style="width:100%;">
                    <thead>
                        <tr style="padding:10px">
                            <th style="padding:5px;">@lang( "lang_v1.id" )</th>
                            <th style="padding:5px;">@lang( 'purchase.ref_no' )</th> 
                            <th style="padding:5px;">@lang( 'lang_v1.user')</th>
                            <th style="padding:5px;">@lang( 'lang_v1.state_action' )</th> 
                            <th style="padding:5px;">@lang( 'lang_v1.name' )</th>
                            <th style="padding:5px;">@lang( 'lang_v1.description' )</th>
                            <th style="padding:5px;">@lang( 'lang_v1.duration' )</th>
                            <th style="padding:5px;">@lang( 'lang_v1.date' )</th> 
                            <th style="padding:5px;">@lang( 'lang_v1.parent_id' )</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="padding:10px;background:grey ; color :white;border-top:1px solid white">
                            <td style="padding:6px;">{{$main_source->id}}</td>
                            <td style="padding:6px;">{{$main_source->ref_number}}</td>
                            <td style="padding:6px;">{{$main_source->user->first_name}}</td>
                            <td style="padding:6px;">{{$main_source->state_action}}</td>
                            <td style="padding:6px;">{{$main_source->name}}</td>
                            <td style="padding:6px;">{{$main_source->description}}</td>
                            <td style="padding:6px;">{{$main_source->duration . ' ' . __('lang_v1.' .$main_source->duration_type)}}</td>
                            <td style="padding:6px;">{{$main_source->created_at}}</td>
                            <td style="padding:6px;">{{$main_source->parent_id}}</td>
                         </tr>
                    </tbody>
                </table>
                
            </div>
        </div>

        @if($main_source->parent_id != null)
        <h3>@lang("lang_v1.previous_movement")</h3>
        <table style="width:100%;">
            <thead>
                <tr style="padding:10px">
                    <th style="padding:5px;">@lang( "lang_v1.id" )</th>
                    <th style="padding:5px;">@lang( 'purchase.ref_no' )</th> 
                    <th style="padding:5px;">@lang( 'lang_v1.user')</th>
                    <th style="padding:5px;">@lang( 'lang_v1.state_action' )</th> 
                    <th style="padding:5px;">@lang( 'lang_v1.name' )</th>
                    <th style="padding:5px;">@lang( 'lang_v1.description' )</th>
                    <th style="padding:5px;">@lang( 'lang_v1.duration' )</th>
                    <th style="padding:5px;">@lang( 'lang_v1.date' )</th> 
                    <th style="padding:5px;">@lang( 'lang_v1.parent_id' )</th> 
                </tr>
            </thead>
            <tbody>
                @foreach ($allData as $item)
                     @if($item->parent_id && $main_source->parent_id == $item->parent_id)
                        @php 
                            $i      = $item->parent_id; 
                        @endphp
                        @while ($i != null)
                            @php
                                $parent = \App\Warranty::find($i); 
                            @endphp
                            @if(!empty($parent))
                                <tr style="padding:10px;background:rgb(210, 210, 210) ; color :rgb(0, 0, 0);border-top:1px solid white">
                                    <td style="padding:6px;">{{$parent->id}}</td>
                                    <td style="padding:6px;">{{$parent->ref_number}}</td>
                                    <td style="padding:6px;">{{$parent->user->first_name}}</td>
                                    <td style="padding:6px;">{{$parent->state_action}}</td>
                                    <td style="padding:6px;">{{$parent->name}}</td>
                                    <td style="padding:6px;">{{$parent->description}}</td>
                                    <td style="padding:6px;">{{$parent->duration . ' ' . __('lang_v1.' .$parent->duration_type)}}</td>
                                    <td style="padding:6px;">{{$parent->created_at}}</td>
                                    <td style="padding:6px;">{{$parent->parent_id}}</td>
                                </tr>
                            @endif
                            @php    
                                $id     = \App\Models\ArchiveTransaction::have_parent($parent); 
                                $i      = $id;
                            @endphp
                        @endwhile
                    @endif
                @endforeach
                     
            </tbody>
        </table>
        @endif

        </div>
  
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
   
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->