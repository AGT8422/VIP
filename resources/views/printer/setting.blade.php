<html>
<head>
    
    <?php  
        /**
         * ******************** *
         * here global variable *
         * ******************** *
         */    
        $margin_top_page            = (isset($containTemp))?$containTemp->margin_top_page:"60px"; 
        $margin_bottom_page         = (isset($containTemp))?$containTemp->margin_bottom_page:"100px";
        $body_content_top           = (isset($containTemp))?(floatVal($margin_top_page)+10)."px":"70px" ;
        $body_content_margin_left   = (isset($containTemp))?$containTemp->body_content_margin_left:"0px" ;
        $body_content_margin_right  = (isset($containTemp))?$containTemp->body_content_margin_right:"0px" ;
        $body_content_margin_bottom = (isset($containTemp))?(floatVal($margin_bottom_page)+40)."px":"100px" ;
      

        
    ?>
     
    <style>

        body     { 
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0px;
            counter-increment: page;
        }
        .content { 
            text-align: center;
         }
        @page   {
            margin: {{ $margin_top_page . "25px" . $margin_bottom_page . "25px"   }};
             
        }
        
        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 100px;
            background-color: #ffffff00;
            text-align: center;
            padding: 10px;
            /* line-height: 35px; */
          
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 100px;
            background-color: #ffffff00;
            text-align: center;
            padding: 10px;
            /* line-height: 35px; */
        }
        .footer {
            position: fixed;
            bottom: 100px;
            left: 0px;
            right: 0px;
            height: 100px;
            background-color: #ffffff00;
            text-align: center;
            padding: 10px;
            /* line-height: 35px; */
        }
        .content {
            position: relative;
            top: {{ $body_content_top   }};
            margin-left:   {{ $body_content_margin_left   }};
            margin-right:  {{ $body_content_margin_right  }};
            margin-bottom: {{ $body_content_margin_bottom }};
            border-radius:1px;
            border:0px solid black;
        }
        .hide {
            display:none;
        }
        .page-number:after {
            content: counter(page);
        }
        
        
        
    </style>
   
</head>
<body>
    <?php 
           /** requirment */
        $footer_display = 0  ; /*1* for choose footer hide or display*/
        $header_display = 0  ; /*2* for choose header hide or display*/
     
    ?>
    {{-- header --}}
    <header @if($header_display == 1) class="hide" @endif>
        @if(isset($totalPages)) 
            @include("printer.header",[ "totalPages"=>$totalPages ,"print_content"=>$print_content,"print"=>$print, "id"=>"edit","PrinterTemplate"=>$template,"PrinterTemplateContain"=>$containTemp,"PrinterContentTemplate"=>$contentTemp,"PrinterFooterTemplate"=>$footerTemp,"transaction" => $transaction ])
        @else
            @include("printer.header",[ "print_content"=>$print_content,"print"=>$print, "id"=>"edit","PrinterTemplate"=>$template,"PrinterTemplateContain"=>$containTemp,"PrinterContentTemplate"=>$contentTemp,"PrinterFooterTemplate"=>$footerTemp,"transaction" => $transaction ])
        @endif
    </header>
    
    {{-- footer --}}
    <footer @if($footer_display == 1) class="hide" @endif >
        @include("printer.footer",[ "print"=>$print,"print_content"=>$print_content, "id"=>"edit","PrinterTemplate"=>$template,"PrinterTemplateContain"=>$containTemp,"PrinterContentTemplate"=>$contentTemp,"PrinterFooterTemplate"=>$footerTemp,"transaction" => $transaction ])
    </footer>

    {{-- body --}}
    <div class="content" >
        @if(isset($totalPages)) 
             @include("printer.body",["totalPages"=>$totalPages ,"print"=>$print, "print_footer"=>$print_footer, "id"=>"edit","PrinterTemplate"=>$template,"PrinterTemplateContain"=>$containTemp,"PrinterContentTemplate"=>$contentTemp,"PrinterFooterTemplate"=>$footerTemp,"transaction" => $transaction ])
        @else
             @include("printer.body",["print"=>$print, "print_footer"=>$print_footer, "id"=>"edit","PrinterTemplate"=>$template,"PrinterTemplateContain"=>$containTemp,"PrinterContentTemplate"=>$contentTemp,"PrinterFooterTemplate"=>$footerTemp,"transaction" => $transaction ])
        @endif
        
    </div>
    
    <div id="div">  </div>
    
    
    <?php 
        /** 
         * ********************************* *
         * here just for use jquery library  *  
         * setting value boxes manually      * 
         * ********************************* *
        */
    ?>
    
    <script type="text/javascript">
         
    </script>
</body>
</html>