<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    

    // function deleteUser(id){
    //     sweetAlertLoading();
    //     $.ajax({
    //         url: "/admin/role/delete_user",
    //         type: 'post',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: {
    //             id: id
    //         },
    //         dataType: 'json',
    //         success:function(response){
    //             //sweetAlert(response.status,response.message);
    //             Swal.fire({
    //                 position: 'center',
    //                 icon: response.status,
    //                 title: response.message,
    //                 showConfirmButton: false,
    //                 timer: 4000
    //             });
    //             table_user.ajax.reload(null, false);
    //         }
    //     });
    // }

    function sweetAlertLoading(){
        Swal.fire({
            title: 'Please Wait!',
            //html: 'data uploading',// add html attribute if you want or remove
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading()
            },
        });   
    }

    function swal2(status, message){
        Swal.fire({
            position: 'center',
            icon: status,
            title: message,
            showConfirmButton: false,
            timer: 4000
        });
    }

    function sweetAlert2(icon, title){
        Swal.fire({
            position: 'center',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: 4000
        });
    }

</script>

<style>
    .swal-confirmButton{
        border: 0;
        border-radius: .25em;
        background: initial;
        background-color: #2778c4;
        color: #fff;
        font-size: 1.0625em;
        margin: .3125em;
        padding: .625em 1.1em;
        box-shadow: none;
        font-weight: 500;
    }
    .swal-confirmButton:focus {
        outline: 0;
        box-shadow: 0 0 0 3px rgba(100,150,200,.5);
    }
    .swal-confirmButton:hover{
        background-image: linear-gradient(rgba(0,0,0,.1),rgba(0,0,0,.1));
    }
    .swal-cancelButton{
        border-style: solid;
        border-width: thin;
        border-radius: .25em;
        background: initial;
        background-color: #fff;
        border-color: rgba(14,14,14,.26);
        color: #464b51;
        font-size: 1.0625em;
        margin: .3125em;
        padding: .625em 1.1em;
        box-shadow: none;
        font-weight: 500;
    }
    .swal-cancelButton:hover{
        background-color: rgba(14,14,14,.26);
        color: #fff;
    }
    .swal-cancelButton:focus {
        outline: 0;
        box-shadow: 0 0 0 3px rgba(14,14,14,.26);
    }
</style>
<style>
    .img-flag {
        height: 56px;
        width: 56px;
        margin-top:-4px;
        margin-right:5px;
    }
</style>