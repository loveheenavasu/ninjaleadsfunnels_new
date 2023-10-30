<div>
    <x-form-section submit="submit">
        <x-slot name="title">
            Users data
        </x-slot>

        <x-slot name="description">
            Users Credentials
        </x-slot>
        <x-slot name="form">
            <div class="col-span-3">
                    <x-jet-label for="first_name" value="{{ __('First Name') }}" />
                    <x-jet-input name="first_name" type="text" class="mt-1 block w-full" wire:model.defer="user.first_name" />
                    <x-jet-input-error for="user.first_name" class="mt-2" />
            </div>
            <div class="col-span-3">
                    <x-jet-label for="last_name" value="{{ __('Last Name') }}" />
                    <x-jet-input name="last_name" type="text" class="mt-1 block w-full" wire:model.defer="user.last_name" />
                    <x-jet-input-error for="user.last_name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <x-jet-label for="role" value="{{ __('Role') }}" />
                    <x-select name="role" class="mt-1" wire:model="user.role">
                        <option value=""></option>
                        @foreach($this->roles() as $role)
                            <option value="{{ $role }}">
                                {{ $role }}
                            </option>
                        @endforeach
                    </x-select>
                <x-jet-input-error for="user.role" class="mt-2" />
            </div>
            @if($this->user->exists == false)
            <div class="col-span-6">
                    <x-jet-label for="email" value="{{ __('Email_Id') }}" />
                    <x-jet-input name="email" type="text" class="mt-1 block w-full" wire:model.defer="user.email" />
                    <x-jet-input-error for="user.email" class="mt-2" />
            </div>
            @endif

            @if($this->user->exists == false)
            <div class="col-span-6">
                    <x-jet-label for="password" value="{{ __('Password') }}" />
                    <x-jet-input name="password" id="password" type="password" class="mt-1 block w-full" wire:model.defer="user.password" />
                    <x-jet-input-error for="user.password" class="mt-2" />
            </div>
            @else
            <button type="button" name="updatepwd" id="updatepwd" class="btn btn-primary" data-toggle="modal" data-target="#paswordUpdateModal">
                      Update Password
            </button>

            <div class="modal fade" id="paswordUpdateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                          
                        <div class="modal-body">
                            <form method="POST" action="" name = "passwordupdate" id="passwordupdate">
                            <input id="user_id" type="hidden" class="form-control" name="user_id" value="{{$this->user->id}}">
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>
  
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="current_password">
                                </div>
                            </div>
  
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>
      
                                <div class="col-md-6">
                                    <input id="new_password" type="password" class="form-control" name="new_password">
                                </div>
                            </div>
  
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">New Confirm Password</label>
        
                                <div class="col-md-6">
                                    <input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" >
                                    <span id='message'></span>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" name = "submitUpdatepwd" id="submitUpdatepwd" class="btn btn-primary">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                            </form>
                        </div>          
                    </div>
                </div>
            </div>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-jet-button>
                {{ __(!$this->user->exists ? 'Create' : 'Update') }}
            </x-jet-button>
        </x-slot>
    </x-form-section>
</div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#new_confirm_password').on('keyup', function () {
      if ($('#new_password').val() == $('#new_confirm_password').val()) {
        $('#message').html('Matching').css('color', 'green');
      } else 
        $('#message').html('Not Matching').css('color', 'red');
    });

    $('#submitUpdatepwd').on('click',function(e){
        e.preventDefault();
        var new_password = $('#new_password').val();
        var password = $('#password').val();
        var new_confirm_password = $('#new_confirm_password').val();
        var user_id = $('#user_id').val();
        if(new_password=='' || password=='' || new_confirm_password==''){
            swal("Please fill all the fields!");
        }
        else{
            $.ajax({
                url: "/changepassword",
                type:"post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {new_password:new_password,user_id:user_id}, 
                success: function(result){
                    window.location.reload();
                }

            });
        }
    });
});
</script>