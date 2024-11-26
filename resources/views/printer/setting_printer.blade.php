@extends('layouts.app')
@section('title', __('printer.Setting_printers'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    @if(!isset($edit_type))
        <b>{{"Create "}}</b>
    @else
        <b>{{"Edit "}} </b> 
    @endif
    <h1>@lang('printer.Setting_printers')
    <small>@lang('printer.manage_your_setting_printers')</small>
    </h1>
    @if(isset($edit_type))
        <h3> {!! "  <b>" . $PrinterTemplate->name_template . "  </b> " . "Template"!!}</h3>
    @endif
</section>
 
<style>
    
    /* .gg{
        border-style:dashed;
        border-style:dotted;
        border-style:double;
        border-style:groove;
        border-style:hidden;
        border-style:ridge;
        border-style:outset;
        border-style:inset;
    } */
    /* .gg{
        color:aliceblue;
        color:antiquewhite;
        color:aqua;
        color:aquamarine;
        color:azure;
        color:beige;
        color:bisque;
        color:black;
        color:blanchedalmond;
        color:blue;
        color:blueviolet;
        color:brown;
        color:burlywood;
        color:cadetblue;
        color:chartreuse;
        color:chocolate;
        color:coral;
        color:cornflowerblue;
        color:cornsilk;
        color:crimson;
        color:cyan;
        color:darkblue;
        color:darkcyan;
        color:darkgoldenrod;
        color:darkgray;
        color:darkgreen;
        color:darkgrey;
        color:darkkhaki;
        color:darkmagenta;
        color:darkolivegreen;
        color:darkorange;
        color:darkorchid;
        color:darkred;
        color:darksalmon;
        color:darkseagreen;
        color:darkslateblue;
        color:darkslategray;
        color:darkturquoise;
        color:darkviolet;
        color:deeppink;
        color:deepskyblue;
        color:dimgray;
        color:dodgerblue;
        color:firebrick;

    } */
</style>
@php 
    
    $form_style = [
        "table" => "Table",
        "h1"    => "Simple Bold",
        "div"   => "Normal",
    ];
    $show = [
        "true"  => "Show",
        "false" => "Hide",
    ];  
    $text_style = [
        "capitalize"  => "Capitalize",
        "lowercase"   => "Lowercase",
        "uppercase"   => "Uppercase",
    ];
    $text_align = [
        "left"     => "Left",
        "center"   => "Center",
        "right"    => "Right"
    ];
    $positions = [
        "relative"     => "Relative",
        "absolute"     => "Absolute",
    ];
    $font_size = [
        "32px"     => "32px",
        "30px"     => "30px",
        "28px"     => "28px",
        "26px"     => "26px",
        "24px"     => "24px",
        "22px"     => "22px",
        "20px"     => "20px",
        "18px"     => "18px",
        "16px"     => "16px",
        "14px"     => "14px",
        "12px"     => "12px",
        "10px"     => "10px",
        "8px"      => "8px",
        "6px"      => "6px",
    ];
    $colors = [
        "transparent"     => "None",
        "red"             => "Red",
        "black"           => "Black",
        "white"           => "White",
        "#f7f7f7"         => "PowderWhite",
        "aliceblue"       => "aliceblue",
        "antiquewhite"    => "antiquewhite",
        "aqua"            => "aqua",
        "aquamarine"      => "aquamarine",
        "azure"           => "azure",
        "beige"           => "beige",
        "bisque"          => "bisque",
        "black"           => "black",
        "blanchedalmond"  => "blanchedalmond",
        "blue"            => "blue",
        "blueviolet"      => "blueviolet",
        "brown"           => "brown",
        "burlywood"       => "burlywood",
        "cadetblue"       => "cadetblue",
        "chartreuse"      => "chartreuse",
        "chocolate"       => "chocolate",
        "coral"           => "coral",
        "cornflowerblue"  => "cornflowerblue",
        "cornsilk"        => "cornsilk",
        "crimson"         => "crimson",
        "cyan"            => "cyan",
        "darkblue"        => "darkblue",
        "darkcyan"        => "darkcyan",
        "darkgoldenrod"   => "darkgoldenrod",
        "darkgray"        => "darkgray",
        "darkgreen"       => "darkgreen",
        "darkgrey"        => "darkgrey",
        "darkkhaki"       => "darkkhaki",
        "darkmagenta"     => "darkmagenta",
        "darkolivegreen"  => "darkolivegreen",
        "darkorange"      => "darkorange",
        "darkorchid"      => "darkorchid",
        "darkred"         => "darkred",
        "darksalmon"      => "darksalmon",
        "darkseagreen"    => "darkseagreen",
        "darkslateblue"   => "darkslateblue",
        "darkslategray"   => "darkslategray",
        "darkturquoise"   => "darkturquoise",
        "darkviolet"      => "darkviolet",
        "deeppink"        => "deeppink",
        "deepskyblue"     => "deepskyblue",
        "dimgray"         => "dimgray",
        "dodgerblue"      => "dodgerblue",
        "firebrick"       => "firebrick",
    ];
    $image_align = [
        "left"         => "Left",
        "center"       => "Center",
        "right"        => "Right",
        "top"          => "Top",
        "bottom"       => "Bottom",
    ];
    $border_style = [
        "solid"   => "Solid", 
        "dashed"  => "Dashed",
        "dotted"  => "Dotted",
        "double"  => "Double",
        "groove"  => "Groove",
        "hidden"  => "Hidden",
        "ridge"   => "Ridge",
        "outset"  => "Outset",
        "inset"   => "Inset",
    ];
    $font_weight = [
        "bolder" => "Bolder", 
        "bold"   => "Bold", 
        "800"    => "800",
        "700"    => "700",
        "600"    => "600",
        "500"    => "500",
        "400"    => "400",
        "300"    => "300",
        "200"    => "200",
        "100"    => "100",
    ];

@endphp 
<!-- Main content -->
<section class="content">
        @if(!isset($edit_type))
            {!! Form::open(['url' => action('Report\PrinterSettingController@store'), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
        @else
            {!! Form::open(['url' => action('Report\PrinterSettingController@update',["id"=>$edit_type]), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
        @endif
        {{-- top setting bar --}}
        <div class="nav printer-setting-bar">
            <div class="row">
                <div class="col-md-2">
                   {!!  Form::label('name_template',"Template Name");  !!}
                   {!!  Form::text('name_template',(isset($edit_type))?$PrinterTemplate->name_template:null,["class" =>"form-control " , 'id'=>'name_template']);  !!}
                </div>
                <div class="col-md-2">
                   {!!  Form::label('Paper-size',"Page Size");  !!}
                   {!!  Form::select('Paper-size',[0=>"A4",1=>"Letter"],(isset($edit_type))?$PrinterTemplate->page_size:null,["class" =>"form-control select2 page_size_select" , 'id'=>'Paper-size']);  !!}
                </div>
                <div class="col-md-2">
                   {!!  Form::label('Form-type',"Form Type");  !!}
                   {!!  Form::select('Form-type',["Sale"=>"Sale","Return_Sale"=>"Return_Sale","Purchase"=>"Purchase","Return_Purchase"=>"Return_Purchase","Voucher"=>"Voucher","Cheque"=>"Cheque"],(isset($edit_type))?$PrinterTemplate->Form_type:null,["class" =>"form-control select2 form_type_select" , 'id'=>'Form-type']);  !!}
                </div>
                <div @if(isset($edit_type) && (  $PrinterTemplate->Form_type != "Sale" || $PrinterTemplate->Form_type != "Return_Sale" ) ) class="col-md-1 sale-relation hide" @else class="col-md-1 sale-relation "@endif>
                   {!!  Form::label('Pattern-type',"Pattern Type");  !!}
                   {!!  Form::select('Pattern-type',$patterns,(isset($edit_type))?$PrinterTemplate->type:null,["class" =>"form-control select2 pattern_type_select" ,"style"=>"width:100%" , 'id'=>'Pattern-type']);  !!}
                </div>
                <div class="col-md-2 voucher-relation hide">
                   {!!  Form::label('Voucher-type',"Voucher Type");  !!}
                   {!!  Form::select('Voucher-type',["Payment"=>"Payment","Receipt"=>"Receipt"],(isset($edit_type))?(($PrinterTemplate->Form_type=="Voucher")?$PrinterTemplate->type:null):null,["class" =>"form-control select2 voucher_type_select" , 'id'=>'Voucher-type']);  !!}
                </div>
                <div class="col-md-2 cheque-relation hide">
                   {!!  Form::label('Cheque-type',"Cheque Type");  !!}
                   {!!  Form::select('Cheque-type',["In"=>"In","Out"=>"Out"],(isset($edit_type))?(($PrinterTemplate->Form_type=="Cheque")?$PrinterTemplate->type:null):null,["class" =>"form-control select2 cheque_type_select" , 'id'=>'Cheque-type']);  !!}
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-2"><span id="change_setting1">{{"Change Setting To Content"}}</span><span class="hide" id="change_setting2">{{"Change Setting To Sizes"}}</span></div>
                <div class="col-md-2">{{"Setting"}} <br>
                    <span id="set_all_default">{{ "Reset All" }}</span> 
                </div>
            </div>
        </div>
        {{-- pages --}}
        <div class="row">

            {{-- .1..........  --}}
            <div class="col-md-7 paper-style" id="paper-style"  >
               
                    {{-- HEADER --}}
                        <div class="col-md-12 se-header">
                            <div class="title-header-setting">
                                @if(isset($edit_type))
                                    @include("printer.header",["id"=>$edit_type,"PrinterTemplate"=>$PrinterTemplate,"PrinterTemplateContain"=>$PrinterTemplateContain,"PrinterContentTemplate"=>$PrinterContentTemplate,"PrinterFooterTemplate"=>$PrinterFooterTemplate])
                                @else
                                    @include("printer.header")
                                @endif
                            </div>
                        </div>
                    {{-- CONTENT --}}
                    <div class="col-md-12">
                        <div class="title-body-setting"> 
                            @if(isset($edit_type))
                                
                                    @include("printer.body",["id"=>$edit_type,"PrinterTemplate"=>$PrinterTemplate,"PrinterContentTemplate"=>$PrinterContentTemplate,"PrinterFooterTemplate"=>$PrinterFooterTemplate])
                                
                            @else
                                 
                                    @include("printer.body")
                                
                            @endif
                        </div>
                    </div>
                    {{-- FOOTER --}}
                        <div class="col-md-12">
                            <div  > 
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                                 <br>
                            </div>
                        </div>
            </div>
            <div class="col-md-5 paper-setting"  >
                        {{-- HEADER --}}
                        <div class="col-md-12"><b style="font-size: 19px">{{"HEADR"}} </b> <span id="set_header_default">{{ "Reset" }}</span> <br></div>
                        <div class="section">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterTemplate->header_view == 1 ) checked @endif  @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="header_view" id="header_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-Text"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Style"}}</b><br>
                                                {!!Form::select("style_header",$form_style,(isset($edit_type))?$PrinterTemplate->style_header:"table",["class"=>"select_printer","id"=>"style_header"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("header_width",(isset($edit_type))?$PrinterTemplate->header_width:"100%",["class"=>"printer-form","id"=>"header_width"]) !!}
                                                 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Display Letters"}}</b>
                                                {!!Form::select("header_style_letter",$text_style,(isset($edit_type))?$PrinterTemplate->header_style_letter:"capitalize",["class"=>"select_printer","id"=>"header_style_letter"]) !!}
                                            </select> 
                                            </td>
                                        </tr>
                                        <tr class="table-section-setting hide">
                                            <td class="left">
                                                <br>
                                                <b>{{"*  Table Width"}}</b><br>
                                                {!!Form::text("header_table_width",(isset($edit_type))?$PrinterTemplate->header_table_width:"100%",["class"=>"printer-form","id"=>"header_table_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"*  Table Color"}}</b><br>
                                                {!!Form::select("header_table_color",$colors,(isset($edit_type))?$PrinterTemplate->header_table_color:"transparent",["class"=>"select_printer","id"=>"header_table_color"]) !!}
                                                
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Table Radius"}}</b><br>
                                                {!!Form::text("header_table_radius",(isset($edit_type))?$PrinterTemplate->header_table_radius:"0px",["class"=>"printer-form","id"=>"header_table_radius"]) !!}
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Text Align"}}</b><br>
                                                {!!Form::select("align_text_header",$text_align,(isset($edit_type))?$PrinterTemplate->align_text_header:"left",["class"=>"select_printer","id"=>"align_text_header"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Font Size"}}</b><br>
                                                {!!Form::select("header_font_size",$font_size,(isset($edit_type))?$PrinterTemplate->header_font_size:"22px",["class"=>"select_printer","id"=>"header_font_size"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Font Weight"}}</b><br>
                                                {!!Form::select("header_font_weight",$font_weight,(isset($edit_type))?$PrinterTemplate->header_font_weight:"300",["class"=>"select_printer","id"=>"header_font_weight"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("header_border_width",(isset($edit_type))?$PrinterTemplate->header_border_width:"0px",["class"=>"printer-form","id"=>"header_border_width"]) !!}
                                             </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("header_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_border_style:"solid",["class"=>"select_printer","id"=>"header_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("header_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_border_color:"transparent",["class"=>"select_printer","id"=>"header_border_color"]) !!}
                                                 
                                              
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Padding Left"}}</b><br>
                                                {!!Form::text("header_padding_left",(isset($edit_type))?$PrinterTemplate->header_padding_left:"0px",["class"=>"printer-form","id"=>"header_padding_left"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Padding Top"}}</b><br>
                                                {!!Form::text("header_padding_top",(isset($edit_type))?$PrinterTemplate->header_padding_top:"0px",["class"=>"printer-form","id"=>"header_padding_top"]) !!}
                                                <br><b>{{"* Padding Bottom"}}</b>
                                                {!!Form::text("header_padding_bottom",(isset($edit_type))?$PrinterTemplate->header_padding_bottom:"0px",["class"=>"printer-form","id"=>"header_padding_bottom"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Padding Right"}}</b>
                                                 {!!Form::text("header_padding_right",(isset($edit_type))?$PrinterTemplate->header_padding_right:"0px",["class"=>"printer-form","id"=>"header_padding_right"]) !!}                                                
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td class="left" >
                                                <br> <b>{{"* Position"}}</b> 
                                                {!!Form::select("header_position",$positions,(isset($edit_type))?$PrinterTemplate->header_position:"relative",["class"=>"select_printer","id"=>"header_position"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"*  Top"}}</b><br>
                                                {!!Form::text("header_top",(isset($edit_type))?$PrinterTemplate->header_top:"0px",["class"=>"printer-form","id"=>"header_top"]) !!}
                                                <br><b>{{"*  Bottom"}}</b>
                                                {!!Form::text("header_bottom",(isset($edit_type))?$PrinterTemplate->header_bottom:"0px",["class"=>"printer-form","id"=>"header_bottom"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Left"}}</b><br>
                                                {!!Form::text("header_left",(isset($edit_type))?$PrinterTemplate->header_left:"0px",["class"=>"printer-form","id"=>"header_left"]) !!}
                                                <br><b>{{"*  Right"}}</b>
                                                {!!Form::text("header_right",(isset($edit_type))?$PrinterTemplate->header_right:"0px",["class"=>"printer-form","id"=>"header_right"]) !!}
                                            </td>
                                        </tr>
                                        <tr class="hide">
                                            <td class="left" >
                                                <br>
                                                <b>{{"*  Box Width"}}</b>
                                                {!!Form::text("header_box_width",(isset($edit_type))?$PrinterTemplate->header_box_width:null,["class"=>"printer-form","id"=>"header_box_width"]) !!}
                                                <br>
                                                <br><b>{{"* Box Border style"}}</b>
                                                {!!Form::select("header_box_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_box_border_style:null,["class"=>"select_printer","id"=>"header_box_border_style"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Box Border width"}}</b><br>
                                                {!!Form::text("header_box_border_width",(isset($edit_type))?$PrinterTemplate->header_box_border_width:null,["class"=>"printer-form","id"=>"header_box_border_width"]) !!}
                                                <br><br><b>{{"*  Box Background"}}</b> 
                                                {!!Form::select("header_box_background",$colors,(isset($edit_type))?$PrinterTemplate->header_box_background:null,["class"=>"select_printer","id"=>"header_box_background"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"*  Box Border color"}}</b><br> 
                                                {!!Form::select("header_box_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_box_border_color:null,["class"=>"select_printer","id"=>"header_box_border_color"]) !!}
                                                <br><br><b>{{"*  Box Border Radius"}}</b>
                                                {!!Form::text("header_box_border_radius",(isset($edit_type))?$PrinterTemplate->header_box_border_radius:null,["class"=>"printer-form","id"=>"header_box_border_radius"]) !!}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="section">
                        <div class="col-md-12">
                            <br>
                            <div class="col-md-2">
                                <span  ><b>{{"* View"}}</b></span>
                                <input type="checkbox" @if(isset($edit_type) && $PrinterTemplate->header_image_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="header_image_view" id="header_image_view">
                            </div>
                            <div class="col-md-6"><hr></div>
                            <div class="col-md-4"><b>{{"Header-Image"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                        </div>
                        <div class="col-md-12 section-setting hide">
                            <table class="printer_cell">
                                <tbody>
                                    <tr>
                                        <td class="left">
                                            <br> 
                                            <b>{{"* Image Align"}}</b><br>
                                            {!!Form::select("align_image_header",$image_align,(isset($edit_type))?$PrinterTemplate->align_image_header:"right",["class"=>"select_printer","id"=>"align_image_header"]) !!}
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                             <b>{{"* Position Align"}}</b><br>
                                             {!!Form::select("position_img_header",$text_align,(isset($edit_type))?$PrinterTemplate->position_img_header:"right",["class"=>"select_printer","id"=>"position_img_header"]) !!}

                                        </td>
                                        <td class="right">
                                             <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("header_image_width",(isset($edit_type))?$PrinterTemplate->header_image_width:"100",["class"=>"printer-form","id"=>"header_image_width"]) !!}
                                             <br><b>{{"* Height"}}</b><br>
                                             {!!Form::text("header_image_height",(isset($edit_type))?$PrinterTemplate->header_image_height:"100",["class"=>"printer-form","id"=>"header_image_height"]) !!}
                                        </td>
                                    </tr>
                                    <tr class="hide">
                                        <td class="left">
                                            <br><b>{{"* Border Width"}}</b><br>
                                            {!!Form::text("header_image_border_width",(isset($edit_type))?$PrinterTemplate->header_image_border_width:"0px",["class"=>"printer-form","id"=>"header_image_border_width"]) !!}
                                        </td>
                                        <td class="center">
                                            <br><b>{{"* Border Color"}}</b><br>
                                            {!!Form::select("header_image_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_image_border_color:"transparent",["class"=>"select_printer","id"=>"header_image_border_color"]) !!}
                                            <br> <b>{{"* Border Style"}}</b><br>
                                            {!!Form::select("header_image_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_image_border_style:"solid",["class"=>"select_printer","id"=>"header_image_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* Border Radius"}}</b><br>
                                            {!!Form::text("header_image_border_radius",(isset($edit_type))?$PrinterTemplate->header_image_border_radius:"0px",["class"=>"printer-form","id"=>"header_image_border_radius"]) !!}
                                        </td>
                                    </tr>
                                    <tr class="hide">
                                        <td class="left">
                                            <br><b>{{"* BOX Width"}}</b><br>
                                            {!!Form::text("header_image_box_width",(isset($edit_type))?$PrinterTemplate->header_image_box_width:"32.333%",["class"=>"printer-form","id"=>"header_image_box_width"]) !!}
                                            <br><b>{{"* BOX Height"}}</b><br>
                                            {!!Form::text("header_image_box_height",(isset($edit_type))?$PrinterTemplate->header_image_box_height:"100%",["class"=>"printer-form","id"=>"header_image_box_height"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* BOX Margin"}}</b><br>
                                            {!!Form::text("header_image_box_margin",(isset($edit_type))?$PrinterTemplate->header_image_box_margin:"auto",["class"=>"printer-form","id"=>"header_image_box_margin"]) !!}
                                        </td>
                                        <td class="right">
                                            <br><b>{{"* BOX Background"}}</b><br>
                                            {!!Form::select("header_box_image_background",$colors,(isset($edit_type))?$PrinterTemplate->header_box_image_background:"transparent",["class"=>"select_printer","id"=>"header_box_image_background"]) !!}
                                            <br><b>{{"* BOX Color"}}</b><br>
                                            {!!Form::select("header_box_image_color",$colors,(isset($edit_type))?$PrinterTemplate->header_box_image_color:"transparent",["class"=>"select_printer","id"=>"header_box_image_color"]) !!}
                                        </td>
                                    </tr>
                                    <tr class="hide">
                                        <td class="left">
                                            <br>
                                            <b>{{"* BOX Position Align"}}</b><br>
                                            {!!Form::select("position_box_header_align",$text_align,(isset($edit_type))?$PrinterTemplate->position_box_header_align:"center",["class"=>"select_printer","id"=>"position_box_header_align"]) !!}
                                            <br><br><b>{{"* BOX Bk Color"}}</b><br>
                                            {!!Form::select("header_image_box_background",$colors,(isset($edit_type))?$PrinterTemplate->header_image_box_background:"transparent",["class"=>"select_printer","id"=>"header_image_box_background"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* BOX Br Width"}}</b><br>
                                            {!!Form::text("header_image_box_border_width",(isset($edit_type))?$PrinterTemplate->header_image_box_border_width:"0px",["class"=>"printer-form","id"=>"header_image_box_border_width"]) !!}
                                            <br>
                                            <br><b>{{"* BOX Br Style"}}</b><br>
                                            {!!Form::text("header_image_box_border_style",(isset($edit_type))?$PrinterTemplate->header_image_box_border_style:"solid",["class"=>"printer-form","id"=>"header_image_box_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* BOX Br Color"}}</b><br>
                                            {!!Form::select("header_image_box_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_image_box_border_color:"transparent",["class"=>"select_printer","id"=>"header_image_box_border_color"]) !!}
                                            <br><br><b>{{"* BOX Br Radius"}}</b><br>
                                            {!!Form::text("header_image_box_border_radius",(isset($edit_type))?$PrinterTemplate->header_image_box_border_radius:"0px",["class"=>"printer-form","id"=>"header_image_box_border_radius"]) !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                        </div>
                        <div class="section">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterTemplate->header_other_view == 1 ) checked @endif  @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="header_other_view" id="header_other_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-Other"}}</b><br>  <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr class="hide">
                                            <td class="left"> 
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("align_other_header",$text_align,(isset($edit_type))?$PrinterTemplate->align_other_header:"center",["class"=>"select_printer","id"=>"align_other_header"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                 <b>{{"* Background Color"}}</b><br>
                                                 {!!Form::select("other_background_header",$colors,(isset($edit_type))?$PrinterTemplate->other_background_header:"transparent",["class"=>"select_printer","id"=>"other_background_header"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("header_other_width",(isset($edit_type))?$PrinterTemplate->header_other_width:"32.333%",["class"=>"printer-form","id"=>"header_other_width"]) !!}
                                                <br><b>{{"* Border Radius"}}</b><br>
                                                {!!Form::text("header_other_border_radius",(isset($edit_type))?$PrinterTemplate->header_other_border_radius:"0px",["class"=>"printer-form","id"=>"header_other_border_radius"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("header_other_border_width",(isset($edit_type))?$PrinterTemplate->header_other_border_width:"0px",["class"=>"printer-form","id"=>"header_other_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("header_other_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_other_border_style:"solid",["class"=>"select_printer","id"=>"header_other_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br><b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("header_other_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_other_border_color:"transparent",["class"=>"select_printer","id"=>"header_other_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Position"}}</b><br>
                                                {!!Form::select("header_other_position",$positions,(isset($edit_type))?$PrinterTemplate->header_other_position:"relative",["class"=>"select_printer","id"=>"header_other_position"]) !!}
                                                 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Top"}}</b><br>
                                                {!!Form::text("header_other_top",(isset($edit_type))?$PrinterTemplate->header_other_top:"0px",["class"=>"printer-form","id"=>"header_other_top"]) !!}
                                                <br>
                                                <b>{{"* Left"}}</b><br>
                                                {!!Form::text("header_other_left",(isset($edit_type))?$PrinterTemplate->header_other_left:"0px",["class"=>"printer-form","id"=>"header_other_left"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Right"}}</b><br>
                                                {!!Form::text("header_other_right",(isset($edit_type))?$PrinterTemplate->header_other_right:"0px",["class"=>"printer-form","id"=>"header_other_right"]) !!}
                                                <br><b>{{"* Bottom"}}</b><br>
                                                {!!Form::text("header_other_bottom",(isset($edit_type))?$PrinterTemplate->header_other_bottom:"0px",["class"=>"printer-form","id"=>"header_other_bottom"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## TOP SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("header_tax_align",$text_align,(isset($edit_type))?$PrinterTemplate->header_tax_align:"center",["class"=>"select_printer","id"=>"header_tax_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br> 
                                                {!!Form::select("header_tax_font_size",$font_size,(isset($edit_type))?$PrinterTemplate->header_tax_font_size:"22px",["class"=>"select_printer","id"=>"header_tax_font_size"]) !!}
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("header_tax_width",(isset($edit_type))?$PrinterTemplate->header_tax_width:"100%",["class"=>"printer-form","id"=>"header_tax_width"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("header_tax_letter",$text_style,(isset($edit_type))?$PrinterTemplate->header_tax_letter:"capitalize",["class"=>"select_printer","id"=>"header_tax_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("header_tax_border_width",(isset($edit_type))?$PrinterTemplate->header_tax_border_width:"0px",["class"=>"printer-form","id"=>"header_tax_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("header_tax_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_tax_border_style:"solid",["class"=>"select_printer","id"=>"header_tax_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b> 
                                                {!!Form::select("header_tax_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_tax_border_color:"transparent",["class"=>"select_printer","id"=>"header_tax_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b> 
                                                {!!Form::text("header_tax_padding_top",(isset($edit_type))?$PrinterTemplate->header_tax_padding_top:"0px",["class"=>"printer-form","id"=>"header_tax_padding_top"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b> 
                                                {!!Form::text("header_tax_padding_left",(isset($edit_type))?$PrinterTemplate->header_tax_padding_left:"0px",["class"=>"printer-form","id"=>"header_tax_padding_left"]) !!}
                                                <br><b>{{" -- Padding Right"}}</b> 
                                                {!!Form::text("header_tax_padding_right",(isset($edit_type))?$PrinterTemplate->header_tax_padding_right:"0px",["class"=>"printer-form","id"=>"header_tax_padding_right"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b> 
                                                {!!Form::text("header_tax_padding_bottom",(isset($edit_type))?$PrinterTemplate->header_tax_padding_bottom:"0px",["class"=>"printer-form","id"=>"header_tax_padding_bottom"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br> 
                                                {!!Form::select("header_tax_position",$positions,(isset($edit_type))?$PrinterTemplate->header_tax_position:"relative",["class"=>"select_printer","id"=>"header_tax_position"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("header_tax_top",(isset($edit_type))?$PrinterTemplate->header_tax_top:"0px",["class"=>"printer-form","id"=>"header_tax_top"]) !!}
                                                <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("header_tax_bottom",(isset($edit_type))?$PrinterTemplate->header_tax_bottom:"0px",["class"=>"printer-form","id"=>"header_tax_bottom"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("header_tax_left",(isset($edit_type))?$PrinterTemplate->header_tax_left:"0px",["class"=>"printer-form","id"=>"header_tax_left"]) !!}
                                                <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("header_tax_right",(isset($edit_type))?$PrinterTemplate->header_tax_right:"0px",["class"=>"printer-form","id"=>"header_tax_right"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## MIDDLE SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("header_address_align",$text_align,(isset($edit_type))?$PrinterTemplate->header_address_align:"center",["class"=>"select_printer","id"=>"header_address_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br> 
                                                {!!Form::select("header_address_font_size",$font_size,(isset($edit_type))?$PrinterTemplate->header_address_font_size:"22px",["class"=>"select_printer","id"=>"header_address_font_size"]) !!}
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("header_address_width",(isset($edit_type))?$PrinterTemplate->header_address_width:"100%",["class"=>"printer-form","id"=>"header_address_width"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("header_address_letter",$text_style,(isset($edit_type))?$PrinterTemplate->header_address_letter:"capitalize",["class"=>"select_printer","id"=>"header_address_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("header_address_border_width",(isset($edit_type))?$PrinterTemplate->header_address_border_width:"0px",["class"=>"printer-form","id"=>"header_address_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("header_address_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_address_border_style:"solid",["class"=>"select_printer","id"=>"header_address_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b>
                                                {!!Form::select("header_address_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_address_border_color:"transparent",["class"=>"select_printer","id"=>"header_address_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b>
                                                {!!Form::text("header_address_padding_top",(isset($edit_type))?$PrinterTemplate->header_address_padding_top:"0px",["class"=>"printer-form","id"=>"header_address_padding_top"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b>
                                                {!!Form::text("header_address_padding_left",(isset($edit_type))?$PrinterTemplate->header_address_padding_left:"0px",["class"=>"printer-form","id"=>"header_address_padding_left"]) !!} 
                                                <br><b>{{" -- Padding Right"}}</b> 
                                                {!!Form::text("header_address_padding_right",(isset($edit_type))?$PrinterTemplate->header_address_padding_right:"0px",["class"=>"printer-form","id"=>"header_address_padding_right"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b>
                                                {!!Form::text("header_address_padding_bottom",(isset($edit_type))?$PrinterTemplate->header_address_padding_bottom:"0px",["class"=>"printer-form","id"=>"header_address_padding_bottom"]) !!}  
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br>
                                                {!!Form::select("header_address_position",$positions,(isset($edit_type))?$PrinterTemplate->header_address_position:"relative",["class"=>"select_printer","id"=>"header_address_position"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("header_address_top",(isset($edit_type))?$PrinterTemplate->header_address_top:"0px",["class"=>"printer-form","id"=>"header_address_top"]) !!}  
                                                 <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("header_address_bottom",(isset($edit_type))?$PrinterTemplate->header_address_bottom:"0px",["class"=>"printer-form","id"=>"header_address_bottom"]) !!}  
                                             </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("header_address_left",(isset($edit_type))?$PrinterTemplate->header_address_left:"0px",["class"=>"printer-form","id"=>"header_address_left"]) !!}  
                                                 <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("header_address_right",(isset($edit_type))?$PrinterTemplate->header_address_right:"0px",["class"=>"printer-form","id"=>"header_address_right"]) !!}  
                                                  
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## BOTTOM SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("header_bill_align",$text_align,(isset($edit_type))?$PrinterTemplate->header_bill_align:"center",["class"=>"select_printer","id"=>"header_bill_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br>
                                                {!!Form::select("header_bill_font_size",$font_size,(isset($edit_type))?$PrinterTemplate->header_bill_font_size:"22px",["class"=>"select_printer","id"=>"header_bill_font_size"]) !!} 
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("header_bill_width",(isset($edit_type))?$PrinterTemplate->header_bill_width:"100%",["class"=>"printer-form","id"=>"header_bill_width"]) !!}  
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("header_bill_letter",$text_style,(isset($edit_type))?$PrinterTemplate->header_bill_letter:"capitalize",["class"=>"select_printer","id"=>"header_bill_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("header_bill_border_width",(isset($edit_type))?$PrinterTemplate->header_bill_border_width:"0px",["class"=>"printer-form","id"=>"header_bill_border_width"]) !!}  
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("header_bill_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_bill_border_style:"solid",["class"=>"select_printer","id"=>"header_bill_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b>
                                                {!!Form::select("header_bill_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_bill_border_color:"transparent",["class"=>"select_printer","id"=>"header_bill_border_color"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b>
                                                {!!Form::text("header_bill_padding_top",(isset($edit_type))?$PrinterTemplate->header_bill_padding_top:"0px",["class"=>"printer-form","id"=>"header_bill_padding_top"]) !!}   
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b> 
                                                {!!Form::text("header_bill_padding_left",(isset($edit_type))?$PrinterTemplate->header_bill_padding_left:"0px",["class"=>"printer-form","id"=>"header_bill_padding_left"]) !!}   
                                                <br><b>{{" -- Padding Right"}}</b> 
                                                {!!Form::text("header_bill_padding_right",(isset($edit_type))?$PrinterTemplate->header_bill_padding_right:"0px",["class"=>"printer-form","id"=>"header_bill_padding_right"]) !!}   
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b> 
                                                {!!Form::text("header_bill_padding_bottom",(isset($edit_type))?$PrinterTemplate->header_bill_padding_bottom:"0px",["class"=>"printer-form","id"=>"header_bill_padding_bottom"]) !!}   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br>
                                                {!!Form::select("header_bill_position",$positions,(isset($edit_type))?$PrinterTemplate->header_bill_position:"relative",["class"=>"select_printer","id"=>"header_bill_position"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("header_bill_top",(isset($edit_type))?$PrinterTemplate->header_bill_top:"0px",["class"=>"printer-form","id"=>"header_bill_top"]) !!}   
                                                <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("header_bill_bottom",(isset($edit_type))?$PrinterTemplate->header_bill_bottom:"0px",["class"=>"printer-form","id"=>"header_bill_bottom"]) !!}   
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("header_bill_left",(isset($edit_type))?$PrinterTemplate->header_bill_left:"0px",["class"=>"printer-form","id"=>"header_bill_left"]) !!}   
                                                <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("header_bill_right",(isset($edit_type))?$PrinterTemplate->header_bill_right:"0px",["class"=>"printer-form","id"=>"header_bill_right"]) !!}   
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="section">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterTemplate->header_line_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="header_line_view" id="header_line_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-line"}}</b><br>  <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td class="left"> 
                                                <b>{{"* Height"}}</b><br>
                                                {!!Form::text("header_line_height",(isset($edit_type))?$PrinterTemplate->header_line_height:"1px",["class"=>"printer-form","id"=>"header_line_height"]) !!}   
                                            </td>
                                            <td class="center">
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("header_line_width",(isset($edit_type))?$PrinterTemplate->header_line_width:"50%",["class"=>"printer-form","id"=>"header_line_width"]) !!}   
                                            </td>
                                            <td class="right">
                                                <b>{{"* Color"}}</b><br>
                                                {!!Form::select("header_line_color",$colors,(isset($edit_type))?$PrinterTemplate->header_line_color:"black",["class"=>"select_printer","id"=>"header_line_color"]) !!} 
                                                 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left"> 
                                                 <br><b>{{"* Top"}}</b><br>
                                                 {!!Form::text("header_line_margin_top",(isset($edit_type))?$PrinterTemplate->header_line_margin_top:"10px",["class"=>"printer-form","id"=>"header_line_margin_top"]) !!}   
                                            </td>
                                            <td class="center" style="text-align: left;font-size:18px;padding-left:10px;padding-top:40px">
                                               <b>{{ "## LINE STYLE ##" }}</b> 
                                            </td>
                                            <td class="right">
                                                 <br><b>{{"* Radius"}}</b><br>
                                                 {!!Form::text("header_line_radius",(isset($edit_type))?$PrinterTemplate->header_line_radius:"0px",["class"=>"printer-form","id"=>"header_line_radius"]) !!}   
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left"> 
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("header_line_border_width",(isset($edit_type))?$PrinterTemplate->header_line_border_width:"1px",["class"=>"printer-form","id"=>"header_line_border_width"]) !!}   
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("header_line_border_style",$border_style,(isset($edit_type))?$PrinterTemplate->header_line_border_style:"solid",["class"=>"select_printer","id"=>"header_line_border_style"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Color"}}</b> 
                                                {!!Form::select("header_line_border_color",$colors,(isset($edit_type))?$PrinterTemplate->header_line_border_color:"black",["class"=>"select_printer","id"=>"header_line_border_color"]) !!} 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
            </div>
            <div class="col-md-5 paper-content hide">
                <b style="font-size:18px;font-weight:800">{{"## Header - Paper Content ##"}}</b><br>
                <br>
                <span class="btn-contain-more">{{ "more ++"}}</span>
                <br><div class="section-content-setting hide">
                    {{-- left section --}}
                    <div class="col-md-12"><br><b style="font-size:15px;font-weight:800">{!! Form::label("left_header","Header Title Side :") !!}</b></div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("left_header_radio","write",(isset($PrinterTemplateContain))?(old('left_header_radio', $PrinterTemplateContain->left_header_radio) == 'write'):null,["id"=>"left_header_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("left_header_radio","database",(isset($PrinterTemplateContain))?(old('left_header_radio', $PrinterTemplateContain->left_header_radio) == 'database'):1,["id"=>"left_header_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_header_radio == 'write') class="col-md-12 style_write_drop hide" @else class="col-md-12 style_write_drop" @endif >{!! Form::select("left_header_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_header_id:null,["class"=>"form-control width_full  select2","id"=>"left_header_id" ,"style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_header_radio == 'write') class="col-md-12 style_write "  @else class="col-md-12 style_write hide" @endif><span class="btn btn-primary" onClick="load_content_header();">{{"test"}}</span>{!! Form::textarea("left_header",(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_header_title:null,["class"=>"form-control", "onChange" => "load_content_header();","id"=>"left_header" ]) !!}</div>
                        
                    </div>
                    {{-- center section --}}
                    <div class="col-md-12"><br>{!! Form::label("center_header","Sub Title Side :") !!}</div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "TOP ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_top_header_radio","write",(isset($PrinterTemplateContain))?(old('center_top_header_radio', $PrinterTemplateContain->center_top_header_radio) == 'write'):null,["id"=>"center_top_header_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_top_header_radio","database",(isset($PrinterTemplateContain))?(old('center_top_header_radio', $PrinterTemplateContain->center_top_header_radio) == 'database'):1,["id"=>"center_top_header_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_top_header_radio == 'write') class="col-md-12 style_write_center_top_header_drop hide" @else class="col-md-12 style_write_center_top_header_drop  " @endif >{!! Form::select("center_top_header_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_top_header_id:null,["class"=>"form-control  select2","id"=>"center_top_header_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_top_header_radio == 'write')  class="col-md-12 style_center_top_header_write  " @else  class="col-md-12 style_center_top_header_write hide " @endif><span class="btn btn-primary" onClick="load_content_header();">{{"test"}}</span>{!! Form::textarea("center_top_header",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_top_header_title:null,["class"=>"form-control","id"=>"center_top_header" ]) !!}</div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "Middle Section ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_middle_header_radio","write",(isset($PrinterTemplateContain))?(old('center_middle_header_radio', $PrinterTemplateContain->center_middle_header_radio) == 'write'):null,["id"=>"center_middle_header_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_middle_header_radio","database",(isset($PrinterTemplateContain))?(old('center_middle_header_radio', $PrinterTemplateContain->center_middle_header_radio) == 'database'):1,["id"=>"center_middle_header_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_middle_header_radio == 'write')  class="col-md-12 style_write_center_middle_header_drop hide" @else class="col-md-12 style_write_center_middle_header_drop" @endif>{!! Form::select("center_middle_header_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_middle_header_id:null,["class"=>"form-control  select2","id"=>"center_middle_header_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_middle_header_radio == 'write') class="col-md-12 style_center_middle_header_write " @else class="col-md-12 style_center_middle_header_write hide" @endif  ><span class="btn btn-primary" onClick="load_content_header();">{{"test"}}</span>{!! Form::textarea("center_middle_header",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_middle_header_title:null,["class"=>"form-control","id"=>"center_middle_header" ]) !!}</div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "last Section ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_last_header_radio","write",(isset($PrinterTemplateContain))?(old('center_last_header_radio', $PrinterTemplateContain->center_last_header_radio) == 'write'):null,["id"=>"center_last_header_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_last_header_radio","database",(isset($PrinterTemplateContain))?(old('center_last_header_radio', $PrinterTemplateContain->center_last_header_radio) == 'database'):1,["id"=>"center_last_header_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_last_header_radio == 'write') class="col-md-12 style_write_center_last_header_drop hide" @else class="col-md-12 style_write_center_last_header_drop"  @endif>{!! Form::select("style_write_center_last_header_drop",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_last_header_id:null,["class"=>"form-control  select2","id"=>"center_last_header_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_last_header_radio == 'write') class="col-md-12 style_center_last_header_write " @else class="col-md-12 style_center_last_header_write hide" @endif ><span class="btn btn-primary" onClick="load_content_header();">{{"test"}}</span>{!! Form::textarea("center_last_header",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_last_header_title:null,["class"=>"form-control", "id"=>"center_last_header"]) !!}</div>
                        
                    </div>
                
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6"><br>
                            <b style=" ">{!!  "Image Side :" !!}</b><br><br>
                            <b style="font-size:15px;font-weight:800">{!! Form::label("","Select Image:") !!}</b>
                            
                        </div>
                        <div class="col-md-6" >
                            <br>
                            {!! Form::file('header_image', ['id' => 'upload_image', 'accept' =>
                            implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                {{-- @includeIf('components.document_help_text') --}}
                                @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->header_image != null)
                                    <div class="img_sec">
                                    <img width="100" src="{{$PrinterTemplateContain->image_url}}">
                                    <span onclick="delete_image(1);"><i class="fa fas fa-trash" style="color: red;cursor:pointer"></i></span>
                                    </div> 
                                @endif
                            </p>
                        </div>
                        
                        
                    </div>

                </div>

            </div>
            {{-- .2..........  --}}
            {{-- <div class="col-md-7 paper-style"  > --}}
               
                    {{-- HEADER --}}
                        
                    {{-- CONTENT --}}
                        
                    {{-- FOOTER --}}
                
            {{-- </div> --}}
            <div class="col-md-5 paper-setting"  >
              
                        {{-- CONTENT --}}
                        <div class="col-md-12" ><b style="font-size: 19px">{{"CONTENT"}}</b> <span id="set_content_default">{{ "Reset" }}</span><br></div>
                        <div class="section">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->top_table_section == 1 ) checked @endif  @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="top_table_section" id="top_table_section"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Content-Top"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                <div class="col-md-12"><br> 
                                                    <span  ><b>{{" Margin Top Of Header (cm)"}}</b></span> <br>
                                                    {!!Form::text("body_content_top",(isset($edit_type))?$PrinterTemplateContain->body_content_top:"2.5cm",["class"=>"printer-form","id"=>"body_content_top"]) !!}
                                                    <br> 
                                                    <span  ><b>{{" Margin Bottom Of Footer (cm)"}}</b></span> <br>
                                                    {!!Form::text("body_content_margin_bottom",(isset($edit_type))?$PrinterTemplateContain->body_content_margin_bottom:"2.5cm",["class"=>"printer-form","id"=>"body_content_margin_bottom"]) !!}
                                                    <br> &nbsp; </div>  
                                                 
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                    <span  ><b>{{" Margin Top Page (cm)"}}</b></span> <br>
                                                    {!!Form::text("margin_top_page",(isset($edit_type))?$PrinterTemplateContain->margin_top_page:"2cm",["class"=>"printer-form","id"=>"margin_top_page"]) !!}
                                                    <br> 
                                                    <span  ><b>{{" Margin Bottom Page (cm)"}}</b></span> <br>
                                                    {!!Form::text("margin_bottom_page",(isset($edit_type))?$PrinterTemplateContain->margin_bottom_page:"2cm",["class"=>"printer-form","id"=>"margin_bottom_page"]) !!}
                                                    <br> &nbsp; </div>
                                                
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                   
                                                    <b>{{" Margin Left "}}</b><br>
                                                    {!!Form::text("body_content_margin_left",(isset($edit_type))?$PrinterTemplateContain->body_content_margin_left:"0px",["class"=>"printer-form","id"=>"body_content_margin_left"]) !!}
                                                    <br>
                                                    <b>{{" Margin Right "}}</b><br>
                                                    {!!Form::text("body_content_margin_right",(isset($edit_type))?$PrinterTemplateContain->body_content_margin_right:"0px",["class"=>"printer-form","id"=>"body_content_margin_right"]) !!}
                                                     <br> &nbsp;</div>
                                                    
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left"  > 
                                                <br>
                                                <b>{{"* Margin bottom"}}</b><br>
                                                {!!Form::text("top_table_margin_bottom",(isset($edit_type))?$PrinterContentTemplate->top_table_margin_bottom:"0px",["class"=>"printer-form","id"=>"top_table_margin_bottom"]) !!}
                                            </td>
                                        
                                            <td class="center">
                                                
                                            </td>
                                            
                                            <td class="right">
                                                <br>
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("top_table_width",(isset($edit_type))?$PrinterContentTemplate->top_table_width:"100%",["class"=>"printer-form","id"=>"top_table_width"]) !!}
                                                 
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Boxes Border Width"}}</b><br>
                                                {!!Form::text("top_table_td_border_width",(isset($edit_type))?$PrinterContentTemplate->top_table_td_border_width:"0px",["class"=>"printer-form","id"=>"top_table_td_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Boxes Border Style"}}</b><br>
                                                {!!Form::select("top_table_td_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->top_table_td_border_style:"solid",["class"=>"select_printer","id"=>"top_table_td_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Boxes Border Color"}}</b><br>
                                                {!!Form::select("top_table_td_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->top_table_td_border_color:"transparent",["class"=>"select_printer","id"=>"top_table_td_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                <div class="col-md-12"><br> 
                                                    <span  ><b>{{"  Hide If Discount Zero "}}</b></span> <br>
                                                    <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->if_discount_zero == 1 ) checked @endif @if(!isset($edit_type))   @endif class="printer-form view-ch" name="if_discount_zero" id="if_discount_zero"> 
                                                    <br> 
                                                    <span  ><b>{{" Show Currency In Rows"}}</b></span> <br>
                                                    <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->currency_in_row == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="currency_in_row" id="currency_in_row"> 
                                                    <br> &nbsp;  
                                                  
                                                    <span  ><b>{{"Repeat Top"}}</b></span> <br>
                                                    <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->repeat_content_top == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="repeat_content_top" id="repeat_content_top"> 
                                                    <br> &nbsp; </div>  
                                                 
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                   
                                                    <b>{{"* Background Color"}}</b><br>
                                                    {!!Form::select("background_color_invoice_info",$colors,(isset($edit_type))?$PrinterContentTemplate->background_color_invoice_info:"transparent",["class"=>"select_printer","id"=>"background_color_invoice_info"]) !!}
                                                    <br>
                                                    <b>{{"* Font Color"}}</b><br>
                                                    {!!Form::select("color_invoice_info",$colors,(isset($edit_type))?$PrinterContentTemplate->color_invoice_info:"black",["class"=>"select_printer","id"=>"color_invoice_info"]) !!}
                                                    <br>
                                                    <span  ><b>{{"View Page Number"}}</b></span> <br>
                                                    <input type="checkbox" @if(isset($edit_type) && $PrinterFooterTemplate->page_number_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="page_number_view" id="page_number_view"> 
                                              
                                                    <br> &nbsp;</div>
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                      
                                                    <b>{{"* Padding Box "}}</b><br>
                                                    {!!Form::text("padding_invoice_info",(isset($edit_type))?$PrinterContentTemplate->padding_invoice_info:"2px",["class"=>"printer-form","id"=>"padding_invoice_info"]) !!}
                                                    
                                                    
                                                    <br>&nbsp; </div>
                                                
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px" >{{" ## Left Section ## "}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("left_top_table_width",(isset($edit_type))?$PrinterContentTemplate->left_top_table_width:"50%",["class"=>"printer-form","id"=>"left_top_table_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("left_top_table_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->left_top_table_text_align:"left",["class"=>"select_printer","id"=>"left_top_table_text_align"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Font-Size"}}</b><br>
                                                 {!!Form::select("left_top_table_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->left_top_table_font_size:"14px",["class"=>"select_printer","id"=>"left_top_table_font_size"]) !!}
                                         
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                <div class="col-md-12"><br> 
                                                <span ><b>{!!Form::text("invoice_no",(isset($edit_type))?$PrinterContentTemplate->invoice_no:'Invoice No :',["class"=>"printer-form","id"=>"invoice_no"]) !!}</b></span> <br> 
                                                <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_number == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_number" id="bold_left_invoice_info_number"> 
                                                <br>
                                                <span ><b>{!!Form::text("project_no",(isset($edit_type))?$PrinterContentTemplate->project_no:'Project No :',["class"=>"printer-form","id"=>"project_no"]) !!}</b></span> <br>
                                                <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_project == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_project" id="bold_left_invoice_info_project"> 
                                                <br><span  ><b>{!!Form::text("date_name",(isset($edit_type))?$PrinterContentTemplate->date_name:'Date :',["class"=>"printer-form","id"=>"date_name"]) !!}</b></span> <br>
                                                <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_date == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_date" id="bold_left_invoice_info_date"> 
                                                <br> &nbsp;</div> 
                                            </td>
                                            <td>
                                                <div class="col-md-12"> <br>
                                                    <b>{{"* Weight Font"}}</b><br>
                                                    {!!Form::select("bold_left_invoice_info",$font_weight,(isset($edit_type))?$PrinterContentTemplate->bold_left_invoice_info:"left",["class"=>"select_printer","id"=>"bold_left_invoice_info"]) !!}
                                                    <br>
                                                    <b>{{"* Border Style"}}</b><br>
                                                    {!!Form::select("bold_left_invoice_info_br_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bold_left_invoice_info_br_style:"left",["class"=>"select_printer","id"=>"bold_left_invoice_info_br_style"]) !!}
                                                    <br>
                                                    <b>{{"* Border Color"}}</b><br>
                                                    {!!Form::select("bold_left_invoice_info_br_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bold_left_invoice_info_br_color:"left",["class"=>"select_printer","id"=>"bold_left_invoice_info_br_color"]) !!}
                                                    <br>
                                                    <b>{{"* Border Width"}}</b><br>
                                                    {!!Form::text("bold_left_invoice_info_br_width",(isset($edit_type))?$PrinterContentTemplate->bold_left_invoice_info_br_width:"0px",["class"=>"printer-form","id"=>"bold_left_invoice_info_br_width"]) !!}
                                                    <br>
                                                 &nbsp;</div>
                                                 
                                                 
                                                 
                                                 
                                                 
                                            </td>
                                            <td>
                                                <div class="col-md-12"> <br>
                                                    <b>{{"* Left Text Align"}}</b><br>
                                                    {!!Form::select("left_invoice_info",$text_align,(isset($edit_type))?$PrinterContentTemplate->left_invoice_info:"left",["class"=>"select_printer","id"=>"left_invoice_info"]) !!}
                                                    <br> 
                                                    <b>{{"* Left Width "}}</b><br>
                                                    {!!Form::text("class_width_left",(isset($edit_type))?$PrinterContentTemplate->class_width_left:"4",["class"=>"printer-form","id"=>"class_width_left"]) !!}
                                                    
                                                    <br>
                                                    <b>{{"* Right Width "}}</b><br>
                                                    {!!Form::text("class_width_right",(isset($edit_type))?$PrinterContentTemplate->class_width_right:"8",["class"=>"printer-form","id"=>"class_width_right"]) !!}
                                                    <br><b>{{"* Right Text Align"}}</b><br>
                                                    {!!Form::select("bold_left_invoice_info_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->bold_left_invoice_info_text_align:"left",["class"=>"select_printer","id"=>"bold_left_invoice_info_text_align"]) !!}
                                                    <br><br>&nbsp;</div>
                                                
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{" ##  Right Section ## "}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("right_top_table_width",(isset($edit_type))?$PrinterContentTemplate->right_top_table_width:"50%",["class"=>"printer-form","id"=>"right_top_table_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("right_top_table_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->right_top_table_text_align:"right",["class"=>"select_printer","id"=>"right_top_table_text_align"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Font-Size"}}</b><br>
                                                 {!!Form::select("right_top_table_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->right_top_table_font_size:"14px",["class"=>"select_printer","id"=>"right_top_table_font_size"]) !!}
                                             
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                    <div class="col-md-12"><br> 
                                                        <span ><b>{!!Form::text("customer_no",(isset($edit_type))?$PrinterContentTemplate->customer_name:'Customer Name :',["class"=>"printer-form","id"=>"customer_no"]) !!}</b></span> <br> 
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_customer_number == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_customer_number" id="bold_left_invoice_info_customer_number"> 
                                                        <br>
                                                        <span ><b>{!!Form::text("address_name",(isset($edit_type))?$PrinterContentTemplate->address_name:'Customer Address :',["class"=>"printer-form","id"=>"address_name"]) !!}</b></span> <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_customer_address == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_customer_address" id="bold_left_invoice_info_customer_address"> 
                                                        <br><span  ><b>{!!Form::text("mobile_name",(isset($edit_type))?$PrinterContentTemplate->mobile_name:'Customer Mobile :',["class"=>"printer-form","id"=>"mobile_name"]) !!}</b></span> <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_customer_mobile == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_customer_mobile" id="bold_left_invoice_info_customer_mobile"> 
                                                        <br><span  ><b>{!!Form::text("tax_name",(isset($edit_type))?$PrinterContentTemplate->tax_name:'Customer Tax :',["class"=>"printer-form","id"=>"tax_name"]) !!}</b></span>  <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bold_left_invoice_info_customer_tax == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bold_left_invoice_info_customer_tax" id="bold_left_invoice_info_customer_tax"> 
                                                        <br> &nbsp;</div> 
                                            </td>
                                            <td>
                                                <div class="col-md-12"> <br>
                                                    <b>{{"* Weight Font"}}</b><br>
                                                    {!!Form::select("bold_right_invoice_info",$font_weight,(isset($edit_type))?$PrinterContentTemplate->bold_right_invoice_info:"left",["class"=>"select_printer","id"=>"bold_right_invoice_info"]) !!}
                                                    <br>
                                                    <b>{{"* Border Style"}}</b><br>
                                                    {!!Form::select("bold_right_invoice_info_br_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bold_right_invoice_info_br_style:"left",["class"=>"select_printer","id"=>"bold_right_invoice_info_br_style"]) !!}
                                                    <br>
                                                    <b>{{"* Border Color"}}</b><br>
                                                    {!!Form::select("bold_right_invoice_info_br_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bold_right_invoice_info_br_color:"left",["class"=>"select_printer","id"=>"bold_right_invoice_info_br_color"]) !!}
                                                    <br>
                                                    <b>{{"* Border Width"}}</b><br>
                                                    {!!Form::text("bold_right_invoice_info_br_width",(isset($edit_type))?$PrinterContentTemplate->bold_right_invoice_info_br_width:"0px",["class"=>"printer-form","id"=>"bold_right_invoice_info_br_width"]) !!}
                                                    <br>&nbsp;</div>
                                                    
                                            </td>
                                            <td>
                                                <div class="col-md-12">  <br>
                                                    <b>{{"* Left Text Align"}}</b><br>
                                                    {!!Form::select("right_invoice_info",$text_align,(isset($edit_type))?$PrinterContentTemplate->right_invoice_info:"left",["class"=>"select_printer","id"=>"right_invoice_info"]) !!}
                                                    <br>
                                                    <b>{{"* Left Width "}}</b><br>
                                                    {!!Form::text("class_width_left_right",(isset($edit_type))?$PrinterContentTemplate->class_width_left_right:"4",["class"=>"printer-form","id"=>"class_width_left_right"]) !!}
                                                    
                                                    <br>
                                                    <b>{{"* Right Width "}}</b><br>
                                                    {!!Form::text("class_width_right_right",(isset($edit_type))?$PrinterContentTemplate->class_width_right_right:"8",["class"=>"printer-form","id"=>"class_width_right_right"]) !!}
                                                    <br> 
                                                    <b>{{"* Right Text Align"}}</b><br>
                                                    {!!Form::select("bold_right_invoice_info_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->bold_right_invoice_info_text_align:"left",["class"=>"select_printer","id"=>"bold_right_invoice_info_text_align"]) !!}
                                                    <br> &nbsp;</div>
                                                
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{" ##  Line ## "}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Top Border Width"}}</b><br>
                                                {!!Form::text("top_table_border_width",(isset($edit_type))?$PrinterContentTemplate->top_table_border_width:"2px",["class"=>"printer-form","id"=>"top_table_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Top Border Style"}}</b><br>
                                                {!!Form::select("top_table_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->top_table_border_style:"solid",["class"=>"select_printer","id"=>"top_table_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br><b>{{"* Top Border Color"}}</b><br>
                                                {!!Form::select("top_table_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->top_table_border_color:"black",["class"=>"select_printer","id"=>"top_table_border_color"]) !!}
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="section">
                        <div class="col-md-12">
                            <br>
                            <div class="col-md-2">
                                <span  ><b>{{"* View"}}</b></span>
                                <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->content_table_section == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="content_table_section" id="content_table_section">
                            </div>
                            <div class="col-md-6"><hr></div>
                            <div class="col-md-4"><b>{{"Content-Body"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                        </div>
                        <div class="col-md-12 section-setting hide">
                            <table class="printer_cell">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <br>
                                            <b style="font-size: 20px">{{ " ## Global Setting ## " }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                           <br>
                                            <b>{{"* Table Width"}}</b><br>
                                            {!!Form::text("content_table_width",(isset($edit_type))?$PrinterContentTemplate->content_table_width:"100%",["class"=>"printer-form","id"=>"content_table_width"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Box Width"}}</b><br>
                                            {!!Form::text("content_width",(isset($edit_type))?$PrinterContentTemplate->content_width:"100%",["class"=>"printer-form","id"=>"content_width"]) !!}
                                            <br> 
                                            <b>{{"* Table Radius"}}</b><br>
                                            {!!Form::text("content_table_border_radius",(isset($edit_type))?$PrinterContentTemplate->content_table_border_radius:"0px",["class"=>"printer-form","id"=>"content_table_border_radius"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* Footer"}}</b> 
                                            {!!Form::select("footer_table",$show,(isset($edit_type))?$PrinterContentTemplate->footer_table:"true",["class"=>"select_printer","id"=>"footer_table"]) !!}
                                          
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Table Title Font-size"}}</b><br>
                                            {!!Form::select("content_table_th_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_th_font_size:"8px",["class"=>"select_printer","id"=>"content_table_th_font_size"]) !!}
                                            <br><br><b>{{"* Table Title br Width"}}</b><br>
                                            {!!Form::text("content_table_th_border_width",(isset($edit_type))?$PrinterContentTemplate->content_table_th_border_width:"1px",["class"=>"printer-form","id"=>"content_table_th_border_width"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Table Title Align"}}</b><br>
                                            {!!Form::select("content_table_th_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_th_text_align:"left",["class"=>"select_printer","id"=>"content_table_th_text_align"]) !!}
                                            <br><br><b>{{"* Table Title br Style"}}</b><br>
                                            {!!Form::select("content_table_th_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->content_table_th_border_style:"solid",["class"=>"select_printer","id"=>"content_table_th_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* Table Title Padding"}}</b><br>
                                            {!!Form::text("content_table_th_padding",(isset($edit_type))?$PrinterContentTemplate->content_table_th_padding:"0px",["class"=>"printer-form","id"=>"content_table_th_padding"]) !!}
                                            <br><br><b>{{"* Table Title br Color"}}</b><br>
                                            {!!Form::select("content_table_th_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->content_table_th_border_color:"black",["class"=>"select_printer","id"=>"content_table_th_border_color"]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Table Body Font-size"}}</b><br>
                                            {!!Form::select("content_table_td_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_font_size:"8px",["class"=>"select_printer","id"=>"content_table_td_font_size"]) !!}
                                            <br><br><b>{{"* Table Body br Width"}}</b><br>
                                            {!!Form::text("content_table_td_border_width",(isset($edit_type))?$PrinterContentTemplate->content_table_td_border_width:"1px",["class"=>"printer-form","id"=>"content_table_td_border_width"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Table Body Align"}}</b><br>
                                            {!!Form::select("content_table_td_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_text_align"]) !!}
                                            <br><br><b>{{"* Table Body br Style"}}</b><br>
                                            {!!Form::select("content_table_td_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->content_table_td_border_style:"solid",["class"=>"select_printer","id"=>"content_table_td_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* Table Body Padding"}}</b><br>
                                            {!!Form::text("content_table_td_padding",(isset($edit_type))?$PrinterContentTemplate->content_table_td_padding:"0px",["class"=>"printer-form","id"=>"content_table_td_padding"]) !!}
                                            <br><br><b>{{"* Table Body br Color"}}</b><br>
                                            {!!Form::select("content_table_td_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->content_table_td_border_color:"black",["class"=>"select_printer","id"=>"content_table_td_border_color"]) !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <br>
                                            <b style="font-size: 20px">{{ " ## Columns ## " }}</b>
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_no_named","First Column") !!}
                                            {!!Form::text("table_th_no_named",(isset($edit_type))?$PrinterContentTemplate->table_th_no_named:"No",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_no_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> 
                                            {!!Form::select("table_th_no",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_no:"true",["class"=>"select_printer","id"=>"table_th_no"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_no",(isset($edit_type))?$PrinterContentTemplate->content_table_width_no:"5%",["class"=>"printer-form","id"=>"content_table_width_no"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_no",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_no:"500",["class"=>"printer-form","id"=>"content_table_font_weight_no"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_no_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_no_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_no_font_size"]) !!}
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_no_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_no_text_align:"center",["class"=>"select_printer","id"=>"content_table_td_no_text_align"]) !!}
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_code_named","Second Column") !!}
                                            {!! Form::text("table_th_code_named",(isset($edit_type))?$PrinterContentTemplate->table_th_code_named:"Code",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_code_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> <br>
                                            {!!Form::select("table_th_code",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_code:"true",["class"=>"select_printer","id"=>"table_th_code"]) !!} 
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_code",(isset($edit_type))?$PrinterContentTemplate->content_table_width_qty:"5%",["class"=>"printer-form","id"=>"content_table_width_code"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_code",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_qty:"500",["class"=>"printer-form","id"=>"content_table_font_weight_code"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_code_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_code_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_code_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_code_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_code_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_code_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_name_named","Third Column") !!}
                                            {!!Form::text("table_th_name_named",(isset($edit_type))?$PrinterContentTemplate->table_th_name_named:"Name",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_name_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b>
                                            {!!Form::select("table_th_name",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_name:"true",["class"=>"select_printer","id"=>"table_th_name"]) !!} 
                                        </td>
                                        <td class="center">
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_name",(isset($edit_type))?$PrinterContentTemplate->content_table_width_name:"5%",["class"=>"printer-form","id"=>"content_table_width_name"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_name",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_name:"500",["class"=>"printer-form","id"=>"content_table_font_weight_name"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_font_size_name",["8px"=>"8px"],"8px",["class"=>"select_printer","id"=>"content_table_font_size_name"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_text_align_name",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_text_align_name:"left",["class"=>"select_printer","id"=>"content_table_text_align_name"]) !!} 
                                        </td>
                                    </tr>
                                    
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_img_named","Fourth Column") !!}
                                            {!! Form::text("table_th_img_named",(isset($edit_type))?$PrinterContentTemplate->table_th_img_named:"IMG",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_img_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> <br>
                                            {!!Form::select("table_th_img",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_img:"true",["class"=>"select_printer","id"=>"table_th_img"]) !!} 
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_img",(isset($edit_type))?$PrinterContentTemplate->content_table_width_img:"5%",["class"=>"printer-form","id"=>"content_table_width_img"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_img",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_img:"500",["class"=>"printer-form","id"=>"content_table_font_weight_img"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_img_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_img_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_img_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_img_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_img_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_img_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_qty_named","Fifth Column") !!}
                                            {!! Form::text("table_th_qty_named",(isset($edit_type))?$PrinterContentTemplate->table_th_qty_named:"QTY",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_qty_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> <br>
                                            {!!Form::select("table_th_qty",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_qty:"true",["class"=>"select_printer","id"=>"table_th_qty"]) !!} 
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_qty",(isset($edit_type))?$PrinterContentTemplate->content_table_width_qty:"5%",["class"=>"printer-form","id"=>"content_table_width_qty"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_qty",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_qty:"500",["class"=>"printer-form","id"=>"content_table_font_weight_qty"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_qty_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_qty_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_qty_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_qty_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_qty_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_qty_text_align"]) !!} 
                                        </td>
                                    </tr>

                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px"> 
                                            {!! Form::label("table_th_price_named","Sixth Column") !!}
                                            {!!Form::text("table_th_price_named",(isset($edit_type))?$PrinterContentTemplate->table_th_price_named:"Price Before Dis Exclude VAT",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_price_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display "}}</b> 
                                            {!!Form::select("table_th_price",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_price:"true",["class"=>"select_printer","id"=>"table_th_price"]) !!} 
                                            
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_price",(isset($edit_type))?$PrinterContentTemplate->content_table_width_price:"5%",["class"=>"printer-form","id"=>"content_table_width_price"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_price",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_price:"500",["class"=>"printer-form","id"=>"content_table_font_weight_price"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_price_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_price_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_price_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_price_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px"> 
                                         {!! Form::label("table_th_price_bdi_named","Seventh Column") !!}
                                            {!!Form::text("table_th_price_bdi_named",(isset($edit_type))?$PrinterContentTemplate->table_th_price_bdi_named:"Price Before Dis Include VAT",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_price_bdi_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> 
                                            {!!Form::select("table_th_price_bdi",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_price_bdi:"true",["class"=>"select_printer","id"=>"table_th_price_bdi"]) !!} 
                                            
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_price_bdi",(isset($edit_type))?$PrinterContentTemplate->content_table_width_price_bdi:"5%",["class"=>"printer-form","id"=>"content_table_width_price_bdi"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_price_bdi",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_price_bdi:"500",["class"=>"printer-form","id"=>"content_table_font_weight_price_bdi"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_price_bdi_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_bdi_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_price_bdi_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_price_bdi_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_bdi_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_price_bdi_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_discount_named","Eighth Column") !!} 
                                            {!!Form::text("table_th_discount_named",(isset($edit_type))?$PrinterContentTemplate->table_th_discount_named:"Discount",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_discount_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> <br>
                                            {!!Form::select("table_th_discount",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_discount:"true",["class"=>"select_printer","id"=>"table_th_discount"]) !!} 
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_discount",(isset($edit_type))?$PrinterContentTemplate->content_table_width_discount:"5%",["class"=>"printer-form","id"=>"content_table_width_discount"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_discount",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_discount:"500",["class"=>"printer-form","id"=>"content_table_font_weight_discount"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_discount_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_discount_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_discount_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_discount_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_discount_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_discount_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px"> 
                                            {!! Form::label("table_th_price_ade_named","Ninth Column") !!} 
                                            {!!Form::text("table_th_price_ade_named",(isset($edit_type))?$PrinterContentTemplate->table_th_price_ade_named:"Price After Dis Exclude VAT",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_price_ade_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> 
                                            {!!Form::select("table_th_price_ade",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_price_ade:"true",["class"=>"select_printer","id"=>"table_th_price_ade"]) !!} 
                                            
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_price_ade",(isset($edit_type))?$PrinterContentTemplate->content_table_width_price_ade:"5%",["class"=>"printer-form","id"=>"content_table_width_price_ade"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_price_ade",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_price_ade:"500",["class"=>"printer-form","id"=>"content_table_font_weight_price_ade"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_price_ade_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_ade_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_price_ade_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_price_ade_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_ade_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_price_ade_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_price_adi_named","Tenth Column") !!} 
                                            {!!Form::text("table_th_price_adi_named",(isset($edit_type))?$PrinterContentTemplate->table_th_price_adi_named:" Price After Dis Include VAT ",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_price_adi_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> 
                                            {!!Form::select("table_th_price_adi",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_price_adi:"true",["class"=>"select_printer","id"=>"table_th_price_adi"]) !!} 
                                            
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_price_adi",(isset($edit_type))?$PrinterContentTemplate->content_table_width_price_adi:"5%",["class"=>"printer-form","id"=>"content_table_width_price_adi"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_price_adi",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_price_adi:"500",["class"=>"printer-form","id"=>"content_table_font_weight_price_adi"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_price_adi_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_adi_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_price_adi_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_price_adi_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_price_adi_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_price_adi_text_align"]) !!} 
                                        </td>
                                    </tr>
                                    <td colspan="3">
                                        <br>
                                        <b style="font-size: 12px">
                                            {!! Form::label("table_th_subtotal_named","Eleventh Column") !!} 
                                            {!!Form::text("table_th_subtotal_named",(isset($edit_type))?$PrinterContentTemplate->table_th_subtotal_named:"Subtotal",["class"=>"printer-form","style"=>"width:40%","id"=>"table_th_subtotal_named"]) !!}
                                        </b>
                                    </td>
                                    <tr>
                                        <td class="left">
                                            <br>
                                            <b>{{"* Display"}}</b> 
                                            {!!Form::select("table_th_subtotal",$show,(isset($edit_type))?$PrinterContentTemplate->table_th_subtotal:"true",["class"=>"select_printer","id"=>"table_th_subtotal"]) !!} 
                                            
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("content_table_width_subtotal",(isset($edit_type))?$PrinterContentTemplate->content_table_width_subtotal:"5%",["class"=>"printer-form","id"=>"content_table_width_subtotal"]) !!}
                                            <br> 
                                            <b>{{"* Weight"}}</b><br>
                                            {!!Form::text("content_table_font_weight_subtotal",(isset($edit_type))?$PrinterContentTemplate->content_table_font_weight_subtotal:"500",["class"=>"printer-form","id"=>"content_table_font_weight_subtotal"]) !!}
                                        </td>
                                        <td class="right">
                                            <br> 
                                            <b>{{"* Font-Size"}}</b><br>
                                            {!!Form::select("content_table_td_subtotal_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->content_table_td_subtotal_font_size:"16px",["class"=>"select_printer","id"=>"content_table_td_subtotal_font_size"]) !!} 
                                            <br> 
                                            <b>{{"* Align"}}</b><br>
                                            {!!Form::select("content_table_td_subtotal_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->content_table_td_subtotal_text_align:"left",["class"=>"select_printer","id"=>"content_table_td_subtotal_text_align"]) !!} 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                        </div>
                        <div class="section">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bottom_table_section == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bottom_table_section" id="bottom_table_section"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Content-Bottom"}}</b><br>  <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                            <td>{{"&nbsp;"}}</td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                    <div class="col-md-12"><br> 
                                                        <span ><b>{{" Subtotal "}}</b></span> <br> 
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bill_invoice_info_down_subtotal == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bill_invoice_info_down_subtotal" id="bill_invoice_info_down_subtotal"> 
                                                        <br>
                                                        <span ><b>{{"   Discount "}}</b></span> <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bill_invoice_info_down_discount == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bill_invoice_info_down_discount" id="bill_invoice_info_down_discount"> 
                                                        <br><span  ><b>{{"  Subtotal After Dis "}}</b></span> <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bill_invoice_info_down_subtotal_after_dis == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bill_invoice_info_down_subtotal_after_dis" id="bill_invoice_info_down_subtotal_after_dis"> 
                                                        <br><span  ><b>{{"  Vat "}}</b></span>  <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->bill_invoice_info_down_vat == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="bill_invoice_info_down_vat" id="bill_invoice_info_down_vat"> 
                                                        <br> &nbsp;</div> 
                                            </td>
                                            <td>
                                                <div class="col-md-12"> 
                                                 &nbsp;</div>
                                                    
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                    {{-- <b>{{"* Text Align"}}</b><br>
                                                    {!!Form::select("right_invoice_info",$text_align,(isset($edit_type))?$PrinterContentTemplate->right_invoice_info:"left",["class"=>"select_printer","id"=>"right_invoice_info"]) !!} --}}
                                                   <br> &nbsp;</div>
                                                
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{" ##  Left Section  ## "}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("left_bottom_table_width",(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_width:"50%",["class"=>"printer-form","id"=>"left_bottom_table_width"]) !!}
                                                <br> 
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("left_bottom_table_td_bor_width",(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_td_bor_width:"1px",["class"=>"printer-form","id"=>"left_bottom_table_td_bor_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("left_bottom_table_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_text_align:"left",["class"=>"select_printer","id"=>"left_bottom_table_text_align"]) !!}
                                                <br> <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("left_bottom_table_td_bor_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_td_bor_style:"solid",["class"=>"select_printer","id"=>"left_bottom_table_td_bor_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Font-Size"}}</b><br>
                                                 {!!Form::select("left_bottom_table_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_font_size:"20px",["class"=>"select_printer","id"=>"left_bottom_table_font_size"]) !!}
                                                <br> <b>{{"* Border Color"}}</b><br>
                                                 {!!Form::select("left_bottom_table_td_bor_color",$colors,(isset($edit_type))?$PrinterContentTemplate->left_bottom_table_td_bor_color:"black",["class"=>"select_printer","id"=>"left_bottom_table_td_bor_color"]) !!}
                                            </td>
                                        </tr>
                                         
                                        
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{" ##  Right Section ## "}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("right_bottom_table_width",(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_width:"50%",["class"=>"printer-form","id"=>"right_bottom_table_width"]) !!}
                                                <br> 
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("right_bottom_table_td_bor_width",(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_td_bor_width:"1px",["class"=>"printer-form","id"=>"right_bottom_table_td_bor_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("right_bottom_table_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_text_align:"right",["class"=>"select_printer","id"=>"right_bottom_table_text_align"]) !!}
                                                
                                                <br> <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("right_bottom_table_td_bor_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_td_bor_style:"solid",["class"=>"select_printer","id"=>"right_bottom_table_td_bor_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Font-Size"}}</b><br>
                                                 {!!Form::select("right_bottom_table_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_font_size:"20px",["class"=>"select_printer","id"=>"right_bottom_table_font_size"]) !!}
                                                <br> <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("right_bottom_table_td_bor_color",$colors,(isset($edit_type))?$PrinterContentTemplate->right_bottom_table_td_bor_color:"black",["class"=>"select_printer","id"=>"right_bottom_table_td_bor_color"]) !!}
                                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{ " ## Invoice Info ## " }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("bill_table_info_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_info_width:"100%",["class"=>"printer-form","id"=>"bill_table_info_width"]) !!}
                                                <br><br><b>{{"* Margin Bottom"}}</b><br>
                                                {!!Form::text("bill_table_margin_bottom",(isset($edit_type))?$PrinterContentTemplate->bill_table_margin_bottom:"10px",["class"=>"printer-form","id"=>"bill_table_margin_bottom"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("bill_table_info_border_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_info_border_width:"1px",["class"=>"printer-form","id"=>"bill_table_info_border_width"]) !!}
                                                <br><br><b>{{"* Margin Top"}}</b><br>
                                                {!!Form::text("bill_table_margin_top",(isset($edit_type))?$PrinterContentTemplate->bill_table_margin_top:"10px",["class"=>"printer-form","id"=>"bill_table_margin_top"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("bill_table_info_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bill_table_info_border_style:"solid",["class"=>"select_printer","id"=>"bill_table_info_border_style"]) !!}
                                                <br><br><b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("bill_table_info_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bill_table_info_border_color:"black",["class"=>"select_printer","id"=>"bill_table_info_border_color"]) !!}
                                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Border Box Width"}}</b><br>
                                                {!!Form::text("bill_table_border_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_border_width:"1px",["class"=>"printer-form","id"=>"bill_table_border_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Box Style"}}</b><br>
                                                {!!Form::select("bill_table_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bill_table_border_style:"solid",["class"=>"select_printer","id"=>"bill_table_border_style"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Box Color"}}</b><br>
                                                {!!Form::select("bill_table_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bill_table_border_color:"black",["class"=>"select_printer","id"=>"bill_table_border_color"]) !!}
                                            </select> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{ " ## Left Bill Rows ## " }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("bill_table_left_td_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_width:"60%",["class"=>"printer-form","id"=>"bill_table_left_td_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Font-Size"}}</b><br>
                                                {!!Form::select("bill_table_left_td_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_font_size:"18px",["class"=>"select_printer","id"=>"bill_table_left_td_font_size"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <b>{{"* Weight"}}</b><br>
                                                {!!Form::select("bill_table_left_td_weight",$font_weight,(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_weight:"300",["class"=>"select_printer","id"=>"bill_table_left_td_weight"]) !!}
                                         
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Text Align"}}</b><br>
                                                {!!Form::select("bill_table_left_td_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_text_align:"left",["class"=>"select_printer","id"=>"bill_table_left_td_text_align"]) !!}
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("bill_table_left_td_border_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_border_width:"1px",["class"=>"printer-form","id"=>"bill_table_left_td_border_width"]) !!}
                                          
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Padding Left"}}</b><br>
                                                {!!Form::text("bill_table_left_td_padding_left",(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_padding_left:"0px",["class"=>"printer-form","id"=>"bill_table_left_td_padding_left"]) !!}
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("bill_table_left_td_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_border_style:"solid",["class"=>"select_printer","id"=>"bill_table_left_td_border_style"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br> <br>
                                                <br>
                                                <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("bill_table_left_td_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bill_table_left_td_border_color:"black",["class"=>"select_printer","id"=>"bill_table_left_td_border_color"]) !!}
                                        
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{ " ## Right Bill Rows ## " }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("bill_table_right_td_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_width:"40%",["class"=>"printer-form","id"=>"bill_table_right_td_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Font-Size"}}</b><br>
                                                {!!Form::select("bill_table_right_td_font_size",$font_size,(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_font_size:"16px",["class"=>"select_printer","id"=>"bill_table_right_td_font_size"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <b>{{"* Weight"}}</b><br>
                                                {!!Form::select("bill_table_right_td_weight",$font_weight,(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_weight:"300",["class"=>"select_printer","id"=>"bill_table_right_td_weight"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Text Align"}}</b><br>
                                                {!!Form::select("bill_table_right_td_text_align",$text_align,(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_text_align:"right",["class"=>"select_printer","id"=>"bill_table_right_td_text_align"]) !!}
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("bill_table_right_td_border_width",(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_border_width:"1px",["class"=>"printer-form","id"=>"bill_table_right_td_border_width"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Padding Left"}}</b><br>
                                                {!!Form::text("bill_table_right_td_padding_left",(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_padding_left:"0px",["class"=>"printer-form","id"=>"bill_table_right_td_padding_left"]) !!} 
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("bill_table_right_td_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_border_style:"solid",["class"=>"select_printer","id"=>"bill_table_right_td_border_style"]) !!}
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <br>
                                                <br>
                                                <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("bill_table_right_td_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->bill_table_right_td_border_color:"black",["class"=>"select_printer","id"=>"bill_table_right_td_border_color"]) !!}
                                            </select> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{ " ## Line Bill Rows ## " }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("line_bill_table_width",(isset($edit_type))?$PrinterContentTemplate->line_bill_table_width:"100%",["class"=>"printer-form","id"=>"line_bill_table_width"]) !!} 
                                                <br>
                                                <b>{{"* Height"}}</b><br>
                                                {!!Form::text("line_bill_table_height",(isset($edit_type))?$PrinterContentTemplate->line_bill_table_height:"2px",["class"=>"printer-form","id"=>"line_bill_table_height"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Color"}}</b><br>
                                                {!!Form::select("line_bill_table_color",$colors,(isset($edit_type))?$PrinterContentTemplate->line_bill_table_color:"black",["class"=>"select_printer","id"=>"line_bill_table_color"]) !!}
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("line_bill_table_border_width",(isset($edit_type))?$PrinterContentTemplate->line_bill_table_border_width:"1px",["class"=>"printer-form","id"=>"line_bill_table_border_width"]) !!} 
                                            </td  >
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("line_bill_table_border_style",$border_style,(isset($edit_type))?$PrinterContentTemplate->line_bill_table_border_style:"solid",["class"=>"select_printer","id"=>"line_bill_table_border_style"]) !!}
                                                <br>
                                                <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("line_bill_table_border_color",$colors,(isset($edit_type))?$PrinterContentTemplate->line_bill_table_border_color:"black",["class"=>"select_printer","id"=>"line_bill_table_border_color"]) !!}
                                               
                                             
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="1">
                                                 <b>{{"* Margin "}}</b><br>
                                                 {!!Form::text("line_bill_table_td_margin_left",(isset($edit_type))?$PrinterContentTemplate->line_bill_table_td_margin_left:"10px",["class"=>"printer-form","id"=>"line_bill_table_td_margin_left"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <br>
                                                <b style="font-size: 20px">{{ " ## Terms & Signature ## " }}</b>
                                            </td>
                                        </tr>
                                        <tr class="box-stylish">
                                            <td>
                                                    <div class="col-md-12"><br> 
                                                        <span ><b>{{" Quotation Terms "}}</b></span> <br> 
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->show_quotation_terms == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="show_quotation_terms" id="show_quotation_terms"> 
                                                        <br>
                                                        <span ><b>{{"   Signatures "}}</b></span> <br>
                                                        <input type="checkbox" @if(isset($edit_type) && $PrinterContentTemplate->show_customer_signature == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="show_customer_signature" id="show_customer_signature"> 
                                                        <br>
                                                        <br>
                                                    </div> 
                                            </td>
                                            <td>
                                                <div class="col-md-12"> 
                                                    {{-- {!! Form::label("quotation_term",__("Choose Quotation Term")) !!}
                                                    {!! Form::select("quotation_term",$terms,(isset($PrinterTemplateContain))?$PrinterTemplateContain->quotation_terms:null,["class"=>"form-control  select2","id"=>"quotation_term","style"=>"width:100%","placeholder"=>"None"]) !!} --}}
                                                </div>
                                                    
                                            </td>
                                            <td>
                                                <div class="col-md-12"><br> 
                                                    {{-- <b>{{"* Text Align"}}</b><br>
                                                    {!!Form::select("right_invoice_info",$text_align,(isset($edit_type))?$PrinterContentTemplate->right_invoice_info:"left",["class"=>"select_printer","id"=>"right_invoice_info"]) !!} --}}
                                                   <br> &nbsp;</div>
                                                
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
            </div>
            <div class="col-md-5 paper-content hide">
                <b style="font-size:18px;font-weight:800">{{"## Content - Paper Content ##"}}</b><br>
                <br>
                <span class="btn-contain-more">{{ "more ++"}}</span>
                <br><div class="section-content-setting hide">
                    {{-- top section --}}
                    {{-- left --}}
                    <div class="col-md-12"><br><b style="font-size:15px;font-weight:800">{!! Form::label("left_top_content","Content Left Side :") !!}</b></div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("left_top_content_radio","write",(isset($PrinterTemplateContain))?(old('left_top_content_radio', $PrinterTemplateContain->left_top_content_radio) == 'write'):null,["id"=>"left_top_content_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("left_top_content_radio","database",(isset($PrinterTemplateContain))?(old('left_top_content_radio', $PrinterTemplateContain->left_top_content_radio) == 'database'):1,["id"=>"left_top_content_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <div  @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_top_content_radio == 'write') class="col-md-12 style_write_top_left_content_drop hide" @else class="col-md-12 style_write_top_left_content_drop" @endif >{!! Form::select("left_top_content_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_top_content_id:null,["class"=>"form-control  select2","id"=>"left_top_content_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_top_content_radio == 'write') class="col-md-12 style_top_left_content_write " @else class="col-md-12 style_top_left_content_write hide" @endif  ><span class="btn btn-primary" onClick="load_content_body();">{{"test"}}</span>{!! Form::textarea("left_top_content",(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_top_content:null,["class"=>"form-control","id"=>"left_top_content" ]) !!}</div>
                        
                    </div>
                    {{-- right --}}
                    <div class="col-md-12"><br><b style="font-size:15px;font-weight:800">{!! Form::label("right_top_content","Content Bottom Side :") !!}</b></div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("right_top_content_radio","write",(isset($PrinterTemplateContain))?(old('right_top_content_radio', $PrinterTemplateContain->right_top_content_radio) == 'write'):null,["id"=>"right_top_content_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("right_top_content_radio","database",(isset($PrinterTemplateContain))?(old('right_top_content_radio', $PrinterTemplateContain->right_top_content_radio) == 'database'):1,["id"=>"right_top_content_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <div  @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->right_top_content_radio == 'write') class="col-md-12 style_write_top_right_content_drop hide" @else class="col-md-12 style_write_top_right_content_drop" @endif >{!! Form::select("right_top_content_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->right_top_content_id:null,["class"=>"form-control  select2","id"=>"right_top_content_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->right_top_content_radio == 'write') class="col-md-12 style_top_right_content_write  " @else class="col-md-12 style_top_right_content_write hide" @endif  ><span class="btn btn-primary" onClick="load_content_body();">{{"test"}}</span>{!! Form::textarea("right_top_content",(isset($PrinterTemplateContain))?$PrinterTemplateContain->right_top_content:null,["class"=>"form-control" ,"id"=>"right_top_content"]) !!}</div>
                        
                    </div>
                    
                    {{-- bottom --}}
                    <div class="col-md-12"><br><b style="font-size:15px;font-weight:800">{!! Form::label("bottom_content","Content Right Side :") !!}</b></div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("bottom_content_radio","write",(isset($PrinterTemplateContain))?(old('right_top_content_radio', $PrinterTemplateContain->bottom_content_radio) == 'write'):null,["id"=>"bottom_content_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("bottom_content_radio","database",(isset($PrinterTemplateContain))?(old('right_top_content_radio', $PrinterTemplateContain->bottom_content_radio) == 'database'):1,["id"=>"bottom_content_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->bottom_content_radio == 'write') class="col-md-12 style_write_bottom_content_drop hide" @else class="col-md-12 style_write_bottom_content_drop" @endif >{!! Form::select("bottom_content_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->bottom_content_id:null,["class"=>"form-control  select2","id"=>"bottom_content_id","style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->bottom_content_radio == 'write') class="col-md-12 style_bottom_content_write " @else class="col-md-12 style_bottom_content_write hide" @endif  ><span class="btn btn-primary" onClick="load_content_body();">{{"test"}}</span>{!! Form::textarea("bottom_content",(isset($PrinterTemplateContain))?$PrinterTemplateContain->bottom_content:null,["class"=>"form-control","id"=>"bottom_content" ]) !!}</div>
                        
                    </div>
                    
                </div>
            </div>
            {{-- .3...........  --}}
            <div class="col-md-7 paper-style"  >
               
                    {{-- HEADER --}}
                       
                    {{-- CONTENT --}}
                        
                    {{-- FOOTER --}}
                    <div class="col-md-12">
                        <div class="title-footer-setting">
                            @if(isset($edit_type))
                                @include("printer.footer",["id"=>$edit_type,"PrinterTemplate"=>$PrinterTemplate,"PrinterContentTemplate"=>$PrinterContentTemplate,"PrinterFooterTemplate"=>$PrinterFooterTemplate])
                            @else
                                @include("printer.footer")
                            @endif
                        </div>
                    </div>
            </div>
            <div class="col-md-5 paper-setting"  >
                        {{-- FOOTER --}}
                        <div class="col-md-12"><b style="font-size: 19px">{{"FOOTER"}}</b> <span id="set_footer_default">{{ "Reset" }}</span><br></div>
                        <div class="section">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterFooterTemplate->footer_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif  class="printer-form view-ch" name="footer_view" id="footer_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-Text"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td class="left">
                                                <br> 
                                                <b>{{"* Style"}}</b><br>
                                                {!!Form::select("style_footer",$form_style,(isset($edit_type))?$PrinterFooterTemplate->style_footer:"table",["class"=>"select_printer","id"=>"style_footer"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("footer_width",(isset($edit_type))?$PrinterFooterTemplate->footer_width:"100%",["class"=>"printer-form","id"=>"footer_width"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Display Letters"}}</b>
                                                {!!Form::select("footer_style_letter",$text_style,(isset($edit_type))?$PrinterFooterTemplate->footer_style_letter:"capitalize",["class"=>"select_printer","id"=>"footer_style_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr class="table-section-setting hide">
                                            <td class="left">
                                                <br>
                                                <b>{{"*  Table Width"}}</b><br>
                                                {!!Form::text("footer_table_width",(isset($edit_type))?$PrinterFooterTemplate->footer_table_width:"100%",["class"=>"printer-form","id"=>"footer_table_width"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"*  Table Color"}}</b><br>
                                                {!!Form::select("footer_table_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_table_color:"transparent",["class"=>"select_printer","id"=>"footer_table_color"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Table Radius"}}</b><br>
                                                {!!Form::text("footer_table_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_table_radius:"0px",["class"=>"printer-form","id"=>"footer_table_radius"]) !!}                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Text Align"}}</b><br>
                                                {!!Form::select("align_text_footer",$text_align,(isset($edit_type))?$PrinterFooterTemplate->align_text_footer:"left",["class"=>"select_printer","id"=>"align_text_footer"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Font Size"}}</b><br>
                                                {!!Form::select("footer_font_size",$font_size,(isset($edit_type))?$PrinterFooterTemplate->footer_font_size:"22px",["class"=>"select_printer","id"=>"footer_font_size"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Font Weight"}}</b><br>
                                                {!!Form::select("footer_font_weight",$font_weight,(isset($edit_type))?$PrinterFooterTemplate->footer_font_weight:"300",["class"=>"select_printer","id"=>"footer_font_weight"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("footer_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_border_width:"0px",["class"=>"printer-form","id"=>"footer_border_width"]) !!}                                                
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("footer_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_border_style:"solid",["class"=>"select_printer","id"=>"footer_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("footer_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_border_color:"transparent",["class"=>"select_printer","id"=>"footer_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Padding Left"}}</b><br>
                                                {!!Form::text("footer_padding_left",(isset($edit_type))?$PrinterFooterTemplate->footer_padding_left:"0px",["class"=>"printer-form","id"=>"footer_padding_left"]) !!} 
                                               
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Padding Top"}}</b><br>
                                                {!!Form::text("footer_padding_top",(isset($edit_type))?$PrinterFooterTemplate->footer_padding_top:"0px",["class"=>"printer-form","id"=>"footer_padding_top"]) !!} 
                                                <br><b>{{"* Padding Bottom"}}</b>
                                                {!!Form::text("footer_padding_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_padding_bottom:"0px",["class"=>"printer-form","id"=>"footer_padding_bottom"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                 <b>{{"* Padding Right"}}</b>
                                                 {!!Form::text("footer_padding_right",(isset($edit_type))?$PrinterFooterTemplate->footer_padding_right:"0px",["class"=>"printer-form","id"=>"footer_padding_right"]) !!} 
                                                
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td class="left" >
                                                <br> <b>{{"* Position"}}</b> 
                                                {!!Form::select("footer_position",$positions,(isset($edit_type))?$PrinterFooterTemplate->footer_position:"0px",["class"=>"select_printer","id"=>"footer_position"]) !!}
                                                  
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"*  Top"}}</b><br>
                                                {!!Form::text("footer_top",(isset($edit_type))?$PrinterFooterTemplate->footer_top:"0px",["class"=>"printer-form","id"=>"footer_top"]) !!} 
                                                <br><b>{{"*  Bottom"}}</b>
                                                {!!Form::text("footer_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_bottom:"0px",["class"=>"printer-form","id"=>"footer_bottom"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Left"}}</b><br>
                                                {!!Form::text("footer_left",(isset($edit_type))?$PrinterFooterTemplate->footer_left:"0px",["class"=>"printer-form","id"=>"footer_left"]) !!} 
                                                <br><b>{{"*  Right"}}</b>
                                                {!!Form::text("footer_right",(isset($edit_type))?$PrinterFooterTemplate->footer_right:"0px",["class"=>"printer-form","id"=>"footer_right"]) !!} 
                                            </td>
                                        </tr>
                                        <tr >
                                            <td class="left" >
                                                <br>
                                                <b >{{"*  Box Width"}}</b>
                                                {!!Form::text("footer_box_width",(isset($edit_type))?$PrinterFooterTemplate->footer_box_width:"0px",["class"=>"printer-form","id"=>"footer_box_width"]) !!} 
                                                <br>
                                                <br ><b class="hide">{{"* Box Border style"}}</b>
                                                {!!Form::select("footer_box_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_box_border_style:"solid",["class"=>"select_printer hide","id"=>"footer_box_border_style"]) !!}
                                            </td>
                                            <td class="center hide ">
                                                <br>
                                                <b>{{"* Box Border width"}}</b><br>
                                                {!!Form::text("footer_box_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_box_border_width:"0px",["class"=>"printer-form","id"=>"footer_box_border_width"]) !!} 
                                                <br><br><b>{{"*  Box Background"}}</b> 
                                                {!!Form::select("footer_box_background",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_box_background:"transparent",["class"=>"select_printer","id"=>"footer_box_background"]) !!}
                                            </td>
                                            <td class="right hide">
                                                <br>
                                                <b>{{"*  Box Border color"}}</b><br> 
                                                {!!Form::select("footer_box_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_box_border_color:"transparent",["class"=>"select_printer","id"=>"footer_box_border_color"]) !!}
                                                <br><br><b>{{"*  Box Border Radius"}}</b>
                                                {!!Form::text("footer_box_border_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_box_border_radius:"0px",["class"=>"printer-form","id"=>"footer_box_border_radius"]) !!} 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="section">
                        <div class="col-md-12">
                            <br>
                            <div class="col-md-2">
                                <span  ><b>{{"* View"}}</b></span>
                                <input type="checkbox" @if(isset($edit_type) && $PrinterFooterTemplate->footer_image_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="footer_image_view" id="footer_image_view">
                            </div>
                            <div class="col-md-6"><hr></div>
                            <div class="col-md-4"><b>{{"Header-Image"}}</b><br> <span class="btn-more">{{ "more ++"}}</span></div>
                        </div>
                        <div class="col-md-12 section-setting hide">
                            <table class="printer_cell">
                                <tbody>
                                    <tr>
                                        <td class="left">
                                            <br> 
                                            <b>{{"* Image Align"}}</b><br>
                                            {!!Form::select("align_image_footer",$image_align,(isset($edit_type))?$PrinterFooterTemplate->align_image_footer:"right",["class"=>"select_printer","id"=>"align_image_footer"]) !!}
                                        </td>
                                        <td class="center">
                                            <br>
                                             <b>{{"* Position Align"}}</b><br>
                                             {!!Form::select("position_img_footer",$text_align,(isset($edit_type))?$PrinterFooterTemplate->position_img_footer:"right",["class"=>"select_printer","id"=>"position_img_footer"]) !!}
                                        </td>
                                        <td class="right">
                                             <br>
                                            <b>{{"* Width"}}</b><br>
                                            {!!Form::text("footer_image_width",(isset($edit_type))?$PrinterFooterTemplate->footer_image_width:"100",["class"=>"printer-form","id"=>"footer_image_width"]) !!} 
                                             <br><b>{{"* Height"}}</b><br>
                                             {!!Form::text("footer_image_height",(isset($edit_type))?$PrinterFooterTemplate->footer_image_height:"100",["class"=>"printer-form","id"=>"footer_image_height"]) !!} 
                                        </td>
                                    </tr>
                                    <tr class="hide">
                                        <td class="left">
                                            <br><b>{{"* Border Width"}}</b><br>
                                            {!!Form::text("footer_image_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_image_border_width:"0px",["class"=>"printer-form","id"=>"footer_image_border_width"]) !!} 
                                        </td>
                                        <td class="center">
                                            <br><b>{{"* Border Color"}}</b><br>
                                            {!!Form::select("footer_image_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_image_border_color:"transparent",["class"=>"select_printer","id"=>"footer_image_border_color"]) !!}
                                            
                                            <br> <b>{{"* Border Style"}}</b><br>
                                            {!!Form::select("footer_image_border_style",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_image_border_style:"solid",["class"=>"select_printer","id"=>"footer_image_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* Border Radius"}}</b><br>
                                            {!!Form::text("footer_image_border_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_image_border_radius:"0px",["class"=>"printer-form","id"=>"footer_image_border_radius"]) !!} 
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="left">
                                            <br><b  >{{"* BOX Width"}}</b><br>
                                            {!!Form::text("footer_image_box_width",(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_width:"100%",["class"=>"printer-form","id"=>"footer_image_box_width"]) !!} 
                                            <br><b class="hide">{{"* BOX Height"}}</b><br>
                                            {!!Form::text("footer_image_box_height",(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_height:"100%",["class"=>"printer-form hide","id"=>"footer_image_box_height"]) !!} 
                                        </td>
                                        <td class="center  hide">
                                            <br>
                                            <b>{{"* BOX Margin"}}</b><br>
                                            {!!Form::text("footer_image_box_margin",(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_margin:"0px",["class"=>"printer-form","id"=>"footer_image_box_margin"]) !!} 
                                        </td>
                                        <td class="right  hide">
                                            <br><b>{{"* BOX Background"}}</b><br>
                                            {!!Form::select("footer_box_image_background",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_box_image_background:"transparent",["class"=>"select_printer","id"=>"footer_box_image_background"]) !!}
                                            
                                            <br><b>{{"* BOX Color"}}</b><br>
                                            {!!Form::select("footer_box_image_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_box_image_color:"transparent",["class"=>"select_printer","id"=>"footer_box_image_color"]) !!}
                                           
                                        </td>
                                    </tr>
                                    <tr class="hide">
                                        <td class="left">
                                            <br>
                                            <b>{{"* BOX Position Align"}}</b><br>
                                            {!!Form::select("position_box_footer_align",$text_align,(isset($edit_type))?$PrinterFooterTemplate->position_box_footer_align:"center",["class"=>"select_printer","id"=>"position_box_footer_align"]) !!}
                                            <br><br><b>{{"* BOX Bk Color"}}</b><br>
                                            {!!Form::select("footer_image_box_background",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_background:"transparent",["class"=>"select_printer","id"=>"footer_image_box_background"]) !!}
                                             
                                        </td>
                                        <td class="center">
                                            <br>
                                            <b>{{"* BOX Br Width"}}</b><br>
                                            {!!Form::text("footer_image_box_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_border_width:"0px",["class"=>"printer-form","id"=>"footer_image_box_border_width"]) !!} 
                                            <br>
                                            <br><b>{{"* BOX Br Style"}}</b><br>
                                            {!!Form::select("footer_image_box_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_border_style:"solid",["class"=>"select_printer","id"=>"footer_image_box_border_style"]) !!}
                                        </td>
                                        <td class="right">
                                            <br>
                                            <b>{{"* BOX Br Color"}}</b><br>
                                            {!!Form::select("footer_image_box_border_color",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_border_color:"transparent",["class"=>"select_printer","id"=>"footer_image_box_border_color"]) !!}
                                            <br><br><b>{{"* BOX Br Radius"}}</b><br>
                                            {!!Form::text("footer_image_box_border_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_image_box_border_radius:"0px",["class"=>"printer-form","id"=>"footer_image_box_border_radius"]) !!} 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                        </div>
                        <div class="section">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterFooterTemplate->footer_other_view == 1 ) checked @endif  @if(!isset($edit_type)) checked  @endif class="printer-form view-ch" name="footer_other_view" id="footer_other_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-Other"}}</b><br>  <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr >
                                            <td class="left hide"> 
                                                <br>
                                                <b>{{"* Align"}}</b><br>
                                                {!!Form::select("align_other_footer",$text_align,(isset($edit_type))?$PrinterFooterTemplate->align_other_footer:"center",["class"=>"select_printer","id"=>"align_other_footer"]) !!}
                                            </td>
                                            <td class="center hide">
                                                <br>
                                                 <b>{{"* Background Color"}}</b><br>
                                                 {!!Form::select("other_background_footer",$colors,(isset($edit_type))?$PrinterFooterTemplate->other_background_footer:"transparent",["class"=>"select_printer","id"=>"other_background_footer"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b >{{"* Width"}}</b><br>
                                                {!!Form::text("footer_other_width",(isset($edit_type))?$PrinterFooterTemplate->footer_other_width:"100%",["class"=>"printer-form","id"=>"footer_other_width"]) !!} 
                                                 <br><b class="hide">{{"* Border Radius"}}</b><br>
                                                 {!!Form::text("footer_other_border_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_other_border_radius:"0px",["class"=>"printer-form hide","id"=>"footer_other_border_radius"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("footer_other_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_other_border_width:"0px",["class"=>"printer-form","id"=>"footer_other_border_width"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("footer_other_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_other_border_style:"solid",["class"=>"select_printer","id"=>"footer_other_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br><b>{{"* Border Color"}}</b><br>
                                                {!!Form::select("footer_other_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_other_border_color:"transparent",["class"=>"select_printer","id"=>"footer_other_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"* Position"}}</b><br>
                                                {!!Form::select("footer_other_position",$positions,(isset($edit_type))?$PrinterFooterTemplate->footer_other_position:"relative",["class"=>"select_printer","id"=>"footer_other_position"]) !!}
                                                <br>
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Top"}}</b><br>
                                                {!!Form::text("footer_other_top",(isset($edit_type))?$PrinterFooterTemplate->footer_other_top:"0px",["class"=>"printer-form","id"=>"footer_other_top"]) !!} 
                                                <br>
                                                <b>{{"* Left"}}</b><br>
                                                {!!Form::text("footer_other_left",(isset($edit_type))?$PrinterFooterTemplate->footer_other_left:"0px",["class"=>"printer-form","id"=>"footer_other_left"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Right"}}</b><br>
                                                {!!Form::text("footer_other_right",(isset($edit_type))?$PrinterFooterTemplate->footer_other_right:"0px",["class"=>"printer-form","id"=>"footer_other_right"]) !!} 
                                                <br><b>{{"* Bottom"}}</b><br>
                                                {!!Form::text("footer_other_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_other_bottom:"0px",["class"=>"printer-form","id"=>"footer_other_bottom"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## TOP SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("footer_tax_align",$text_align,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_align:"center",["class"=>"select_printer","id"=>"footer_tax_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br> 
                                                {!!Form::select("footer_tax_font_size",$font_size,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_font_size:"22px",["class"=>"select_printer","id"=>"footer_tax_font_size"]) !!}
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("footer_tax_width",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_width:"100%",["class"=>"printer-form","id"=>"footer_tax_width"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("footer_tax_letter",$text_style,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_letter:"capitalize",["class"=>"select_printer","id"=>"footer_tax_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("footer_tax_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_border_width:"0px",["class"=>"printer-form","id"=>"footer_tax_border_width"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("footer_tax_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_border_style:"solid",["class"=>"select_printer","id"=>"footer_tax_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b> 
                                                {!!Form::select("footer_tax_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_border_color:"transparent",["class"=>"select_printer","id"=>"footer_tax_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b> 
                                                {!!Form::text("footer_tax_padding_top",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_padding_top:"0px",["class"=>"printer-form","id"=>"footer_tax_padding_top"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b> 
                                                {!!Form::text("footer_tax_padding_left",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_padding_left:"0px",["class"=>"printer-form","id"=>"footer_tax_padding_left"]) !!} 
                                                <br><b>{{" -- Padding Right"}}</b> 
                                                {!!Form::text("footer_tax_padding_right",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_padding_right:"0px",["class"=>"printer-form","id"=>"footer_tax_padding_right"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b> 
                                                {!!Form::text("footer_tax_padding_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_padding_bottom:"0px",["class"=>"printer-form","id"=>"footer_tax_padding_bottom"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br> 
                                                {!!Form::select("footer_tax_position",$positions,(isset($edit_type))?$PrinterFooterTemplate->footer_tax_position:"relative",["class"=>"select_printer","id"=>"footer_tax_position"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("footer_tax_top",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_top:"0px",["class"=>"printer-form","id"=>"footer_tax_top"]) !!} 
                                                <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("footer_tax_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_bottom:"0px",["class"=>"printer-form","id"=>"footer_tax_bottom"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("footer_tax_left",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_left:"0px",["class"=>"printer-form","id"=>"footer_tax_left"]) !!} 
                                                <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("footer_tax_right",(isset($edit_type))?$PrinterFooterTemplate->footer_tax_right:"0px",["class"=>"printer-form","id"=>"footer_tax_right"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## MIDDLE SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("footer_address_align",$text_align,(isset($edit_type))?$PrinterFooterTemplate->footer_address_align:"center",["class"=>"select_printer","id"=>"footer_address_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br> 
                                                {!!Form::select("footer_address_font_size",$font_size,(isset($edit_type))?$PrinterFooterTemplate->footer_address_font_size:"10px ,]hzlh fjgfs ad yv[ a,t, ,;g ad pg, tdih ]",["class"=>"select_printer","id"=>"footer_address_font_size"]) !!}
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("footer_address_width",(isset($edit_type))?$PrinterFooterTemplate->footer_address_width:"100%",["class"=>"printer-form","id"=>"footer_address_width"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("footer_address_letter",$text_style,(isset($edit_type))?$PrinterFooterTemplate->footer_address_letter:"capitalize",["class"=>"select_printer","id"=>"footer_address_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("footer_address_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_address_border_width:"0px",["class"=>"printer-form","id"=>"footer_address_border_width"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("footer_address_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_address_border_style:"solid",["class"=>"select_printer","id"=>"footer_address_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b> 
                                                {!!Form::select("footer_address_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_address_border_color:"transparent",["class"=>"select_printer","id"=>"footer_address_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b> 
                                                {!!Form::text("footer_address_padding_top",(isset($edit_type))?$PrinterFooterTemplate->footer_address_padding_top:"0px",["class"=>"printer-form","id"=>"footer_address_padding_top"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b> 
                                                {!!Form::text("footer_address_padding_left",(isset($edit_type))?$PrinterFooterTemplate->footer_address_padding_left:"0px",["class"=>"printer-form","id"=>"footer_address_padding_left"]) !!} 
                                                <br><b>{{" -- Padding Right"}}</b> 
                                                {!!Form::text("footer_address_padding_right",(isset($edit_type))?$PrinterFooterTemplate->footer_address_padding_right:"0px",["class"=>"printer-form","id"=>"footer_address_padding_right"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b> 
                                                {!!Form::text("footer_address_padding_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_address_padding_bottom:"0px",["class"=>"printer-form","id"=>"footer_address_padding_bottom"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br> 
                                                {!!Form::select("footer_address_position",$positions,(isset($edit_type))?$PrinterFooterTemplate->footer_address_position:"relative",["class"=>"select_printer","id"=>"footer_address_position"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("footer_address_top",(isset($edit_type))?$PrinterFooterTemplate->footer_address_top:"0px",["class"=>"printer-form","id"=>"footer_address_top"]) !!} 
                                                <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("footer_address_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_address_bottom:"0px",["class"=>"printer-form","id"=>"footer_address_bottom"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("footer_address_left",(isset($edit_type))?$PrinterFooterTemplate->footer_address_left:"0px",["class"=>"printer-form","id"=>"footer_address_left"]) !!} 
                                                <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("footer_address_right",(isset($edit_type))?$PrinterFooterTemplate->footer_address_right:"0px",["class"=>"printer-form","id"=>"footer_address_right"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <BR></BR>
                                                <B>{{" ## BOTTOM SECTION ##"}}</B>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Align"}}</b> <br>
                                                {!!Form::select("footer_bill_align",$text_align,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_align:"center",["class"=>"select_printer","id"=>"footer_bill_align"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Font-size"}}</b><br> 
                                                {!!Form::select("footer_bill_font_size",$font_size,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_font_size:"22px",["class"=>"select_printer","id"=>"footer_bill_font_size"]) !!}
                                                <br><b>{{" -- Width"}}</b><br>
                                                {!!Form::text("footer_bill_width",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_width:"100%",["class"=>"printer-form","id"=>"footer_bill_width"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Letter"}}</b> <br>
                                                {!!Form::select("footer_bill_letter",$text_style,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_letter:"capitalize",["class"=>"select_printer","id"=>"footer_bill_letter"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Border Width"}}</b> 
                                                {!!Form::text("footer_bill_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_border_width:"0px",["class"=>"printer-form","id"=>"footer_bill_border_width"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Border style"}}</b> 
                                                {!!Form::select("footer_bill_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_border_style:"solid",["class"=>"select_printer","id"=>"footer_bill_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Border color"}}</b> 
                                                {!!Form::select("footer_bill_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_border_color:"transparent",["class"=>"select_printer","id"=>"footer_bill_border_color"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{"  -- Padding Top"}}</b> 
                                                {!!Form::text("footer_bill_padding_top",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_padding_top:"0px",["class"=>"printer-form","id"=>"footer_bill_padding_top"]) !!} 
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"  -- Padding Left"}}</b>
                                                {!!Form::text("footer_bill_padding_left",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_padding_left:"0px",["class"=>"printer-form","id"=>"footer_bill_padding_left"]) !!}  
                                                <br><b>{{" -- Padding Right"}}</b>
                                                {!!Form::text("footer_bill_padding_right",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_padding_right:"0px",["class"=>"printer-form","id"=>"footer_bill_padding_right"]) !!}  
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"  -- Padding Bottom"}}</b> 
                                                {!!Form::text("footer_bill_padding_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_padding_bottom:"0px",["class"=>"printer-form","id"=>"footer_bill_padding_bottom"]) !!} 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left">
                                                <br>
                                                <b>{{" -- Position"}}</b><br> 
                                                {!!Form::select("footer_bill_position",$positions,(isset($edit_type))?$PrinterFooterTemplate->footer_bill_position:"relative",["class"=>"select_printer","id"=>"footer_bill_position"]) !!}
                                            </td>
                                            <td class="center">
                                                <br>
                                                <b>{{" -- Top"}}</b> <br>
                                                {!!Form::text("footer_bill_top",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_top:"0px",["class"=>"printer-form","id"=>"footer_bill_top"]) !!} 
                                                <br><b>{{"   -- Bottom"}}</b> <br>
                                                {!!Form::text("footer_bill_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_bottom:"0px",["class"=>"printer-form","id"=>"footer_bill_bottom"]) !!} 
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{" -- Left"}}</b> <br>
                                                {!!Form::text("footer_bill_left",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_left:"0px",["class"=>"printer-form","id"=>"footer_bill_left"]) !!} 
                                                <br><b>{{" -- Right"}}</b> <br>
                                                {!!Form::text("footer_bill_right",(isset($edit_type))?$PrinterFooterTemplate->footer_bill_right:"0px",["class"=>"printer-form","id"=>"footer_bill_right"]) !!} 
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="section">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-2">
                                    <span  ><b>{{"* View"}}</b></span>
                                    <input type="checkbox" @if(isset($edit_type) && $PrinterFooterTemplate->footer_line_view == 1 ) checked @endif @if(!isset($edit_type)) checked  @endif  class="printer-form view-ch" name="footer_line_view" id="footer_line_view"> 
                                </div>
                                <div class="col-md-6"><hr></div>
                                <div class="col-md-4"><b>{{"Header-line"}}</b><br>  <span class="btn-more">{{ "more ++"}}</span></div>
                            </div>
                            <div class="col-md-12 section-setting hide">
                                <table class="printer_cell">
                                    <tbody>
                                        <tr>
                                            <td class="left"> 
                                                <b>{{"* Height"}}</b><br>
                                                {!!Form::text("footer_line_height",(isset($edit_type))?$PrinterFooterTemplate->footer_line_height:"1px",["class"=>"printer-form","id"=>"footer_line_height"]) !!} 
                                            </td>
                                            <td class="center">
                                                <b>{{"* Width"}}</b><br>
                                                {!!Form::text("footer_line_width",(isset($edit_type))?$PrinterFooterTemplate->footer_line_width:"50%",["class"=>"printer-form","id"=>"footer_line_width"]) !!} 
                                            </td>
                                            <td class="right">
                                                <b>{{"* Color"}}</b><br>
                                                {!!Form::select("footer_line_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_line_color:"black",["class"=>"select_printer","id"=>"footer_line_color"]) !!}
                                               
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left"> 
                                                 <br><b>{{"* Top"}}</b><br>
                                                 {!!Form::text("footer_line_margin_top",(isset($edit_type))?$PrinterFooterTemplate->footer_line_margin_top:"10px",["class"=>"printer-form","id"=>"footer_line_margin_top"]) !!}
                                                 
                                            </td>
                                            <td class="center" style="text-align: left;font-size:18px;padding-left:10px;padding-top:40px">
                                                <br><b>{{"* Bottom"}}</b><br>
                                                {!!Form::text("footer_line_margin_bottom",(isset($edit_type))?$PrinterFooterTemplate->footer_line_margin_bottom:"10px",["class"=>"printer-form","id"=>"footer_line_margin_bottom"]) !!}
                                            </td>
                                            <td class="right">
                                                 <br><b>{{"* Radius"}}</b><br>
                                                 {!!Form::text("footer_line_radius",(isset($edit_type))?$PrinterFooterTemplate->footer_line_radius:"0px",["class"=>"printer-form","id"=>"footer_line_radius"]) !!}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="left"> 
                                                <br>
                                                <b>{{"* Border Width"}}</b><br>
                                                {!!Form::text("footer_line_border_width",(isset($edit_type))?$PrinterFooterTemplate->footer_line_border_width:"1px",["class"=>"printer-form","id"=>"footer_line_border_width"]) !!}
                                             </td>
                                            <td class="center">
                                                <br>
                                                <b>{{"* Border Style"}}</b><br>
                                                {!!Form::select("footer_line_border_style",$border_style,(isset($edit_type))?$PrinterFooterTemplate->footer_line_border_style:"solid",["class"=>"select_printer","id"=>"footer_line_border_style"]) !!}
                                            </td>
                                            <td class="right">
                                                <br>
                                                <b>{{"* Border Color"}}</b> 
                                                {!!Form::select("footer_line_border_color",$colors,(isset($edit_type))?$PrinterFooterTemplate->footer_line_border_color:"black",["class"=>"select_printer","id"=>"footer_line_border_color"]) !!}
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
            </div>
            <div class="col-md-5 paper-content hide">
                <b style="font-size:18px;font-weight:800">{{"## Footer - Paper Content ##"}}</b><br>
                <br>
                <span class="btn-contain-more">{{ "more ++"}}</span>
                <br><div class="section-content-setting hide">

                    {{-- left section --}}
                    <div class="col-md-12"><br><b style="font-size:15px;font-weight:800">{!! Form::label("left_footer","Header Title Side :") !!}</b></div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("left_footer_radio","write",(isset($PrinterTemplateContain))?(old('left_footer_radio', $PrinterTemplateContain->left_footer_radio) == 'write'):null,["id"=>"left_footer_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("left_footer_radio","database",(isset($PrinterTemplateContain))?(old('left_footer_radio', $PrinterTemplateContain->left_footer_radio) == 'database'):1,["id"=>"left_footer_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_footer_radio == 'write') class="col-md-12 style_write_footer_drop hide" @else class="col-md-12 style_write_footer_drop" @endif  >{!! Form::select("left_footer_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_footer_id:null,["class"=>"form-control  select2","id"=>"left_footer_id" ,"style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div  @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->left_footer_radio == 'write') class="col-md-12 style_footer_write" @else class="col-md-12 style_footer_write hide" @endif ><span class="btn btn-primary" onClick="load_content_footer();">{{"test"}}</span>{!! Form::textarea("left_footer",(isset($PrinterTemplateContain))?$PrinterTemplateContain->left_footer_title:null,["class"=>"form-control" ,"id"=>"left_footer"]) !!}</div>
                        
                    </div>
                    {{-- center section --}}
                    <div class="col-md-12"><br>{!! Form::label("center_header","Sub Title Side :") !!}</div>
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "TOP ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_top_footer_radio","write",(isset($PrinterTemplateContain))?(old('center_top_footer_radio', $PrinterTemplateContain->center_top_footer_radio) == 'write'):null,["id"=>"center_top_footer_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_top_footer_radio","database",(isset($PrinterTemplateContain))?(old('center_top_footer_radio', $PrinterTemplateContain->center_top_footer_radio) == 'database'):1,["id"=>"center_top_footer_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div  @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_top_footer_radio == 'write') class="col-md-12 style_write_center_top_footer_drop hide" @else class="col-md-12 style_write_center_top_footer_drop" @endif >{!! Form::select("center_top_footer_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_top_footer_id:null,["class"=>"form-control  select2","id"=>"center_top_footer_id" ,"style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div  @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_top_footer_radio == 'write') class="col-md-12 style_center_top_footer_write " @else class="col-md-12 style_center_top_footer_write hide" @endif ><span class="btn btn-primary" onClick="load_content_footer();">{{"test"}}</span>{!! Form::textarea("center_top_footer",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_top_footer_title:null,["class"=>"form-control","id"=>"center_top_footer"]) !!}</div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "Middle Section ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_middle_footer_radio","write",(isset($PrinterTemplateContain))?(old('center_middle_footer_radio', $PrinterTemplateContain->center_middle_footer_radio) == 'write'):null,["id"=>"center_middle_footer_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_middle_footer_radio","database",(isset($PrinterTemplateContain))?(old('center_middle_footer_radio', $PrinterTemplateContain->center_middle_footer_radio) == 'database'):1,["id"=>"center_middle_footer_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_middle_footer_radio == 'write') class="col-md-12 style_write_center_middle_footer_drop hide"  @else class="col-md-12 style_write_center_middle_footer_drop" @endif >{!! Form::select("center_middle_footer_id",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_middle_footer_id:null,["class"=>"form-control  select2","id"=>"center_middle_footer_id" ,"style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_middle_footer_radio == 'write') class="col-md-12 style_center_middle_footer_write " @else  class="col-md-12 style_center_middle_footer_write hide" @endif><span class="btn btn-primary" onClick="load_content_footer();">{{"test"}}</span>{!! Form::textarea("center_middle_footer",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_middle_footer_title:null,["class"=>"form-control","id"=>"center_middle_footer" ]) !!}</div>
                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <br>
                            <b style="font-size:15px;font-weight:800">{!!  "last Section ## :" !!}</b><br>
                            {!! Form::label("","By Edit :") !!}
                            {!! Form::radio("center_last_footer_radio","write",(isset($PrinterTemplateContain))?(old('center_last_footer_radio', $PrinterTemplateContain->center_last_footer_radio) == 'write'):null,["id"=>"center_last_footer_radio"]) !!}<br>
                            {!! Form::label("","By invoice layout :") !!}
                            {!! Form::radio("center_last_footer_radio","database",(isset($PrinterTemplateContain))?(old('center_last_footer_radio', $PrinterTemplateContain->center_last_footer_radio) == 'database'):1,["id"=>"center_last_footer_radio"]) !!}</br>
                        </div>
                        <div class="col-md-6" >
                            <br>
                            <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_last_footer_radio == 'write') class="col-md-12 style_write_center_last_footer_drop hide"  @else class="col-md-12 style_write_center_last_footer_drop" @endif >{!! Form::select("style_write_center_last_footer_drop",$layouts,(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_last_footer_id:null,["class"=>"form-control  select2","id"=>"center_last_footer_id" ,"style"=>"width:100%","placeholder"=>"None"]) !!}</div>
                        </div>
                        <div @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->center_last_footer_radio == 'write') class="col-md-12 style_center_last_footer_write " @else class="col-md-12 style_center_last_footer_write hide" @endif ><span class="btn btn-primary" onClick="load_content_footer();">{{"test"}}</span>{!! Form::textarea("center_last_footer",(isset($PrinterTemplateContain))?$PrinterTemplateContain->center_last_footer_title:null,["class"=>"form-control","id"=>"center_last_footer" ]) !!}</div>
                        
                    </div>
                    {{-- last section --}}
                    <div class="col-md-12" style="border-bottom:2px dashed black">
                        <div class="col-md-6"><br>
                            <b style=" ">{!!  "Image Side :" !!}</b><br><br>
                            <b style="font-size:15px;font-weight:800">{!! Form::label("","Select Image:") !!}</b>
                            
                        </div>
                        <div class="col-md-6" >
                            <br>
                            {!! Form::file('footer_image', ['id' => 'upload_document', 'accept' =>
                            implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                {{-- @includeIf('components.document_help_text') --}}
                                @if((isset($PrinterTemplateContain)) && $PrinterTemplateContain->footer_image != null)
                                <div class="foot_img_sec">
                                <img width="100" src="{{$PrinterTemplateContain->footer_image_url}}">
                                <span onclick="delete_image(2);"><i class="fa fas fa-trash" style="color: red;cursor:pointer"></i></span>
                                </div> 
                            @endif
                            </p>
                        </div>
                        
                        
                    </div>

                </div>
            </div>

            <div class="row">
                @if(isset($edit_type))
                    {{-- <div class="col-12 pull-right-container"> --}}
                        {{-- <a class="btn btn-primary" href="{{action("Report\PrinterSettingController@generatePdf",["id"=>$PrinterTemplate->id])}}">{{" Preview Printing "}}</a> --}}
                        {{-- <a class="btn btn-primary" onclick="PrintModule($('.paper-style').html())" href="#">{{" Preview Printing "}}</a> --}}
                    {{-- </div> --}}
                @else
                @endif
            </div>
            
            <div class="col-md-12">
                @if(!isset($edit_type))
                    {!! Form::submit("Create",["class"=>"btn btn-primary pull-right" ,"style"=>"font-size:18px;margin:50px"]); !!}
                @else
                    {!! Form::submit("Update",["class"=>"btn btn-primary pull-right" ,"style"=>"font-size:18px;margin:50px"]); !!}
                @endif
            </div>

        </div>
        {!! Form::close() !!}
</section>
<!-- /.content -->
@stop

@include('printer.partial.javascript')
