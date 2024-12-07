@section('javascript')
    <script type="text/javascript">
        @if(isset($email))
            @if($email != null && $password != null)
                $("form#first_login_form").submit();
            @endif
        @endif
    </script>
    <script type="text/javascript">
            $(document).ready(function() {
                 
                
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
                $("#email").on('input',debounce(function(){
                    
                    $(this).css({"border": "2px solid #e86000 !important;","color":"#2c2c2c"});
                    $(this).parent().find('.error').html('');
                    
                },debounceDelay));

                $('#first_login_form').on('submit', function(e) {
                    // Prevent default form submission
                    let isValid = true;
                    e.preventDefault();
                    // Clear previous errors
                    $('.error').text('');
                    // Validate form fields
                    const email    = $('#email').val().trim();
                    const password = $('#password').val().trim();
                    
                    if (email === '') {
                        $('#emailError').text('Email is required.');
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        isValid = false;
                    } else if (!validateEmail(email)) {
                        $('#emailError').text('Invalid email format.');
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        isValid = false;
                    } 
                    
                    if (password === '') {
                        $('#passwordError').text('Password is required.');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        isValid = false;
                    } else if (password.length <  6) {
                        $('#passwordError').text('Password must be at least 6 characters.');
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        isValid = false;
                    } 
                    console.log(isValid);
                    if (isValid) {
                        // If the form is valid, submit it
                        this.submit();
                    }
                });
                
                function validateEmail(email) {
                    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                    return re.test(email);
                } 
                // alert(window.location.hostname);   
                // alert($("#domain_name_current").val());   
                var checked     = 0;
                var list_domain = JSON.parse($("#domain_name_array").val());  
                for( i in list_domain){ 
                    if(list_domain[i] == window.location.hostname){ 
                        checked = 1;
                    } 
                }  
                if( window.location.hostname != "localhost" ){
                    if(checked == 0 ){ 
                         
                        $("form#go-home").submit();
                    }   
                }   
           
            });
    </script>
@endsection