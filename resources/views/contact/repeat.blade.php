@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>First Name</h1>
            </div>
            <div class="col-12">
                @php $count=0;   @endphp
                @if(count($array_first_name_repeat)>0)
                    @foreach($array_first_name_repeat as $key => $value)
                        {{$count++}}&nbsp;{{ "_" }} &nbsp; <b>{{$value["id"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["first_name"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{isset($value["name"])?$value["name"]:""}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["contact_id"]}}</b><br>
                    @endforeach
                @endif
                <hr>
            </div>
            <div class="col-12">
                <h1>Name</h1>
            </div>
            <div class="col-12">
                @php $count1=0;  @endphp
                @if(count($array_name_repeat)>0)
                    @foreach($array_name_repeat as $key => $value)
                    {{$count1++}}&nbsp;{{ "_" }} &nbsp;<b>{{$value["id"]}}</b> &nbsp;{{ "_" }} &nbsp;<b>{{$value["first_name"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{isset($value["name"])?$value["name"]:""}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["contact_id"]}}</b><br>
                    @endforeach
                @endif
                <hr>
            </div>
            <div class="col-12">
                <h1>Contact Id</h1>
            </div>
            <div class="col-12">
                @php $count2=0;  @endphp
                @if(count($array_number_repeat)>0)
                    @foreach($array_number_repeat as $key => $value)
                    {{$count2++}}&nbsp;{{ "_" }} &nbsp;<b>{{$value["id"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["first_name"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{isset($value["name"])?$value["name"]:""}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["contact_id"]}}</b><br>
                    @endforeach
                @endif
                <hr>
            </div>
            <div class="col-12">
                <h1>Contact Change</h1>
            </div>
            <div class="col-12">
                @php $count3=0;  @endphp
                @if(count($contact_change_number)>0)
                    @foreach($contact_change_number as $key => $value)
                    {{$count3++}}&nbsp;{{ "_" }} &nbsp;<b>{{$value["id"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["first_name"]}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{isset($value["name"])?$value["name"]:""}}</b> &nbsp;{{ "_" }} &nbsp; <b>{{$value["contact_id"]}}</b><br>
                    @endforeach
                @endif
                <hr>
                 <div class="col-12">
                    <h1>Product Inventory</h1>
                </div>
                <div class="col-12">
                    @php $count2=0; $list_minus = [];  @endphp
                    <table>
                        <tbody>
                            @foreach($list as $key => $value)
                            @php if( $value["enable"] != 0){ $list_minus[] = [ "qty" =>$value["Qty"] , "code" => $value["Sku"] ];}  @endphp
                            <tr>
                                <td>
                                      <b>{{$value["Qty"]}}</b>        <br>
                                </td>
                                <td>
                                     <b>{{$value["Sku"]}}</b>  <br>
                                </td>
                                <td>
                                      <b>{{$value["Product"]}}</b>  <br>
                                </td>
                            </tr> 
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                </div>
            </div>
            <div class="col-12">
                     <table>
                        <tbody>
                            @php
                             $ies = 0;
                            @endphp
                            @foreach($list_minus as $s => $v)
                                @php
                                   $Product         =  \App\Product::where("sku",$v["code"])->where("name",$v["name"])->first();
                                   $ItemMove        =  \App\Models\ItemMove::where("product_id",$Product->id)->orderBy("date","desc")->orderBy("id","desc")->first();
                                   $sum             =  \App\Models\WarehouseInfo::where("product_id",$Product->id)->sum("product_qty");
                                   $warehouse_plus  =  \App\MovementWarehouse::where("product_id",$Product->id)->sum("plus_qty");
                                   $warehouse_minus =  \App\MovementWarehouse::where("product_id",$Product->id)->sum("minus_qty");
                                @endphp
                                @if(round($v["qty"],2) != round($sum,2)  || round($v["qty"],2) != (round($warehouse_plus,2) - round($warehouse_minus,2)) || round($sum,2)   != (round($warehouse_plus,2) - round($warehouse_minus,2)))
                                     @php
                                     $ies++;
                                     @endphp
                                     <tr>
                                        <td style="width:100px">
                                              <b>{{$Product->id}}</b>   <br>
                                              
                                        </td>
                                        <td style="width:100px">
                                              <b>{{$v["code"]}}</b>   <br>
                                              {{$v["name"]}}
                                        </td>
                                        <td style="width:100px">
                                              <p>&nbsp;{{" "}}</p>   <br>
                                        </td>
                                        <td style="width:200px">
                                              <b>{{"  **  ".  $v["qty"] ." ** " }}</b>   <br>
                                              {{ " move    "}}
                                        </td>
                                        <td style="width:200px">
                                              <b>{{"  **  ".   $sum . "  ** "}}</b>    <br>
                                              {{"  Stock  "}}
                                        </td>
                                        <td style="width:200px">
                                              <b>{{ "  **  ".  ($warehouse_plus - $warehouse_minus) . "  ** "}}</b>   <br>
                                               {{"  Store  "}}
                                        </td>
                                        
                                        
                                    </tr>
                                @endif        
                            @endforeach   
                                     <tr>
                                        <td>
                                              <b>{{ $ies }}</b>   <br>
                                        </td>
                                        <td>
                                              <p>&nbsp;{{" *****"}}</p>   <br>
                                        </td>
                                        <td>
                                              <p>&nbsp;{{" ****"}}</p>   <br>
                                        </td>
                                        <td>
                                              <p>&nbsp;{{" ****"}}</p>     <br>
                                        </td>
                                        <td>
                                              <p>&nbsp;{{" ****"}}</p>    <br>
                                        </td>
                                        
                                        
                                    </tr>
                            
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
    
@stop