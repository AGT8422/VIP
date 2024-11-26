@section('javascript')
    <script type="text/javascript">
            $(document).ready(function() {
                 
                let isValid = true;
                let debounceTimer;
                const debounceDelay = 500; // Adjust the delay as needed (milliseconds)
                
                function debounce(func, delay) {
                    return function() {
                        const context = this;
                        const args    = arguments;
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => func.apply(context, args), delay);
                    };
                }
                
                var company_name      = false;
                var emails            = false;
                var mobile            = false;
                var domain_name       = false;
                var passwords         = false;
                var confirm_passwords = false;

                $("#company_name").on('input',debounce(function(){
                    
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');

                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkCompanyName')}}",
                        "dataType":"json",
                        "data":{company_name:$(this).val()},
                        "success":function(data){ 


                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                wrong.parent().find('.error').html('already exist');
                                wrong.addClass('hide_icon');
                                company_name = true;
                            }else{
                                if(value != ""){
                                    // console.log( "length of ### : "+ correct.parent().find('.izo-form-input').val().length );
                                    if(correct.parent().find('.izo-form-input').val().length < 3){
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        wrong.parent().find('.error').html('Please should be at least 3 characters');
                                        wrong.addClass('hide_icon');
                                        company_name = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        wrong.parent().find('.error').html('');
                                        company_name = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    company_name = false;
                                } 
                            } 




                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#email").on('input',debounce(function(){
                    
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');

                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkEmail')}}",
                        "dataType":"json",
                        "data":{email:$(this).val()},
                        "success":function(data){ 



                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                wrong.parent().find('.error').html('already exist');
                                emails = false;
                                wrong.addClass('hide_icon');
                            }else{
                                if(value != ""){
                                        if(!validateEmail(value)){
                                            spin.addClass('hide_icon');
                                            correct.addClass('hide_icon');
                                            wrong.removeClass('hide_icon');
                                            wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                            emails = false;
                                            wrong.parent().find('.error').html('invaild email');
                                            wrong.addClass('hide_icon');
                                        }else{
                                            spin.addClass('hide_icon');
                                            wrong.addClass('hide_icon');
                                            correct.removeClass('hide_icon');
                                            correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                            wrong.parent().find('.error').html('');
                                            emails = true;
                                        }
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                        wrong.parent().find('.error').html('');
                                        emails = false;
                                    } 
                            } 





                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#domain_name").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().parent().parent().find('.spinner');
                    var correct = $(this).parent().parent().parent().find('.success-icon');
                    var wrong   = $(this).parent().parent().parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');
                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkDomainName')}}",
                        "dataType":"json",
                        "data":{domain_name:$(this).val()},
                        "success":function(data){ 
                            if(data.message === false && value != ""){
                                $('#domainError').text('Domain Name is already used.');
                                $('#domainError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                domain_name = true;
                            }else{
                                if(value != ""){
                                    if(correct.parent().find('.izo-form-input').val().length < 3){
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        wrong.parent().find('.error').html('Please should be at least 3 characters');
                                        wrong.addClass('hide_icon');
                                        company_name = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        domain_name = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    domain_name = false;
                                } 
                            } 
                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#mobile").on('input',debounce(function(){
                    
                    
                    var spin    = $(this).parent().parent().find('.spinner');
                    var correct = $(this).parent().parent().find('.success-icon');
                    var wrong   = $(this).parent().parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    max   = $(this).attr('data-max');
                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkMobile')}}",
                        "dataType":"json",
                        "data":{mobile:$(this).val(),mobile_code:$('#mobile_code').val()},
                        "success":function(data){ 
                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                mobile = false;
                            }else{
                                if(value != ""){
                                    // console.log(value.length);
                                    // console.log(parseFloat(max));
                                    if(value.length > parseFloat(max)){
                                        // spin.addClass('hide_icon');
                                        // correct.addClass('hide_icon');
                                        // wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        // mobile = false;
                                        spin.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        // wrong.parent().find('.izo-form-input-mobile').css({"border": "2px solid red","color":"red"}); 
                                        $('#mobileError').text('Must be less than or equal to '+parseFloat(max)+' number.');
                                        $('#mobileError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        mobile = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        $('#mobileError').text(''); 
                                        mobile = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    mobile = true;
                                } 
                            } 
                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#password").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input-password').css({"border": "1px solid #e86000","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');
                    
                    value = $(this).val();
                    if(value.length >  6){
                        if(!/[a-z]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').text('Password must Contain at Letter .');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else if(!/[A-Z]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').text('Password must Contain at least One Capital Letter .');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else if(!/[0-9]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').text('Password must Contain at least One Number .');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else{
                            if(value != $("#confirm-password").val()){
                                spin.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                                $('#passwordError').text('Don\'t Match .');
                                $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                $("#confirm-password").css({"border": "2px solid red","color":"red"});  
                                $('#passwordConfirmError').text('Don\'t Match .');
                                $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                passwords = false;
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.addClass('hide_icon');
                            }else{
                                spin.addClass('hide_icon');
                                wrong.addClass('hide_icon');
                                correct.removeClass('hide_icon');
                                correct.parent().find('.izo-form-input-password').css({"border":"2px solid green","color":"green"});
                                $("#confirm-password").css({"border": "2px solid green","color":"green"});  
                                $('#passwordConfirmError').text('');
                                $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid green;","color":"green"});
                                wrong.parent().find('.error').html('');
                                passwords = true;
                            }
                        }
                    } else{
                        spin.addClass('hide_icon');
                        wrong.removeClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"});
                        $('#passwordError').text('Password must be at least 6 characters.');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        passwords = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                          
                    } 
                    
                },debounceDelay));
                
                $("#confirm-password").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input-password').css({"border": "1px solid #e86000","color":"#2c2c2c"});
                    
                    value = $(this).val();
                    if(value.length > 6){
                        if(value !=  $("#password").val()){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordConfirmError').text('Password Dosn\'t match.');
                            $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            $("#password").css({"border": "2px solid red","color":"red"});  
                            $('#passwordError').text('Don\'t Match .');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                            confirm_passwords = false;
                        }else{
                            spin.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                            correct.removeClass('hide_icon');
                            correct.parent().find('.izo-form-input-password').css({"border":"2px solid green","color":"green"});
                            $('#passwordConfirmError').text('');
                            $("#password").css({"border": "2px solid green","color":"green"}); 
                            $('#passwordError').text('');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid green;","color":"green"});
                            confirm_passwords = true;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }
                    }else{
                        spin.addClass('hide_icon');
                        wrong.removeClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                        $('#passwordConfirmError').text('Password must be at least 6 characters.');
                        $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');

                        // spin.addClass('hide_icon');
                        // wrong.removeClass('hide_icon');
                        // correct.addClass('hide_icon');
                        // wrong.find('.izo-form-input-password').css({"border": "1px solid red","color":"red"});
                        confirm_passwords = false;
                    } 
                    
                },debounceDelay));
                
                
                
                 
                
                $('#first_register_form').on('submit', function(e) {
                    // Prevent default form submission
                    e.preventDefault();

                    if(company_name == true && domain_name  == true && emails == true && passwords == true && mobile == true && confirm_passwords == true ){
                        isValid = true;
                    }else{
                        isValid = false;
                    }

                    // Clear previous errors
                    $('.error').text('');
                    // Validate form fields
                    const name     = $('#company_name').val().trim();
                    const email    = $('#email').val().trim();
                    const password = $('#password').val().trim();
                    
                    if (name === '') {
                        $('#nameError').text('Name is required.');
                        $('#nameError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#nameError').parent().find('.spinner');
                        var correct = $('#nameError').parent().find('.success-icon');
                        var wrong   = $('#nameError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                    
                    if (email === '') {
                        $('#emailError').text('Email is required.');
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#emailError').parent().find('.spinner');
                        var correct = $('#emailError').parent().find('.success-icon');
                        var wrong   = $('#emailError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if (!validateEmail(email)) {
                        $('#emailError').text('Invalid email format.');
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#emailError').parent().find('.spinner');
                        var correct = $('#emailError').parent().find('.success-icon');
                        var wrong   = $('#emailError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                     
                    if (password === '') {
                        $('#passwordError').text('Password is required.');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if (password.length <  6) {
                        $('#passwordError').text('Password must be at least 6 characters.');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[a-z]/.test(password)){
                        $('#passwordError').text('Password must Contain at Letter .');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[A-Z]/.test(password)){
                        $('#passwordError').text('Password must Contain at least One Capital Letter .');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[0-9]/.test(password)){
                        $('#passwordError').text('Password must Contain at least One Number .');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                    
                    // console.log('isValid '  +  isValid);
                    if (isValid) {
                        // If the form is valid, submit it
                        $(".loading").css({"display":"block"});
                        this.submit();
                    }
                });
                
                function validateEmail(email) {
                    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                    return re.test(email);
                }
                var checked     = 0;
                var list_domain = JSON.parse($("#domain_name_array").val());  
                for( i in list_domain){ 
                    if(list_domain[i] == window.location.hostname){ 
                        checked = 1;
                    } 
                }  
                if( window.location.hostname != "izocloud.com" ){
                    if(checked == 0 ){ 
                         
                        $("form#go-home").submit();
                    }   
                }   
            });
    </script>
@endsection