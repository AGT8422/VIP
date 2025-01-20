@section('javascript')
    <script type="text/javascript">
        @if(isset($email))
            @if($email != null && $password != null)
                $("form#first_login_form").submit();
            @endif
        @endif
        setInterval(function() { 
            // parent  =  $('meta[name="csrf-token"]').parent();
            // $('meta[name="csrf-token"]').remove();
            // alert( parent.html());
            // $( '<meta name="csrf-token" content="{{ csrf_token() }}">' ).insertBefore( 'meta[name="author"]' );
            // alert( parent.html());
        }, 1000); 
    </script>
    <script type="text/javascript">
            $(document).ready(function() {
                $(document).on('click','.language_box',function(){
                    $(".list_of_lang").toggleClass('hide');
                });
                
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
                
                if( window.location.hostname != "izocloud.com" ){ 
                    if(checked == 0 ){ 
                        $("form#go-home").submit();
                    }   
                }  
                if( $('#log_out').val() != null  &&  $('#log_out').val() != ""){ 
                    @php
                        session()->forget('adminLogin');
                        session()->forget('log_out_back');
                    @endphp 
                    $("form#go-home-2").submit();
                }    
           
            });
 
            document.addEventListener('DOMContentLoaded', function () {
                    const toggle_password = document.querySelector('.toggle-password');
                    const togglePassword = document.querySelector('#togglePassword');
                    const password = document.querySelector('#password');
                    // const confirm_password = document.querySelector('#confirm_password');

                    togglePassword.addEventListener('click', function () {
                        // Toggle the type attribute using getAttribute() and setAttribute()
                        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                        // const confirm_type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
                        password.setAttribute('type', type);
                        // confirm_password.setAttribute('type', confirm_type); 
                        if(toggle_password.getAttribute('data-password') == "show"){
                            togglePassword.innerHTML = '<svg fill="#000000" height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns\:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 512 512" xml\:space="preserve"><g> <g> <path d="M507.418,241.382C503.467,235.708,409.003,102.4,256,102.4S8.533,235.708,4.582,241.382c-6.11,8.789-6.11,20.446,0,29.235 C8.533,276.292,102.997,409.6,256,409.6s247.467-133.308,251.418-138.982C513.528,261.828,513.528,250.172,507.418,241.382z M256,384C114.62,384,25.6,256,25.6,256S114.62,128,256,128s230.4,128,230.4,128S397.38,384,256,384z"/> </g> </g> <g> <g> <path d="M256,153.6c-56.55,0-102.4,45.841-102.4,102.4S199.441,358.4,256,358.4c56.559,0,102.4-45.841,102.4-102.4 S312.55,153.6,256,153.6z M256,332.8c-42.351,0-76.8-34.449-76.8-76.8s34.449-76.8,76.8-76.8c42.351,0,76.8,34.449,76.8,76.8 C332.8,298.351,298.351,332.8,256,332.8z"/></g></g></svg>';
                            toggle_password.setAttribute('data-password','hidden');
                            
                        }else{
                            togglePassword.innerHTML = '<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L22 22" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.71277 6.7226C3.66479 8.79527 2 12 2 12C2 12 5.63636 19 12 19C14.0503 19 15.8174 18.2734 17.2711 17.2884M11 5.05822C11.3254 5.02013 11.6588 5 12 5C18.3636 5 22 12 22 12C22 12 21.3082 13.3317 20 14.8335" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 14.2362C13.4692 14.7112 12.7684 15.0001 12 15.0001C10.3431 15.0001 9 13.657 9 12.0001C9 11.1764 9.33193 10.4303 9.86932 9.88818" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </svg>';
                            toggle_password.setAttribute('data-password','show');
                            
                        }
                        // Toggle the eye icon
                        this.classList.toggle('eye-icon--active');
                    });
                });
             
    </script>
@endsection