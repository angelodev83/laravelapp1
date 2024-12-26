<div class="modal " id="add_report_modal" style="display:none;" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add new Report</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="" method="POST" id="AddForm">
          <div class="container">
              <div class="row">
                  <div class="col-6">
                      <div class="mb-3">
                           <label for="report_year" class="form-label">Year</label>
                          <input type="text" class="form-control" id="report_year" value="{{date('Y')}}" >
                      </div>
                  </div>
                  <div class="col-6">
                      <div class="mb-3">
                           <label for="report_month_select" class="form-label">Month</label>
                        <select class="form-control form-select" id="report_month_select" name="month_report_select"> 
                            @for ($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $month == date('m') ? 'selected' : '' }}>
                                    {{ date('M', mktime(0, 0, 0, $month, 10)) }}
                                </option>
                            @endfor
                        </select>
                      </div>
                  </div>
                  <div class="col-6">
                      <div class="mb-3">
                          <label label for="store" class="form-label">Store</label>
                          <select class="form-select" data-placeholder="Select store.." name="store" id="store"></select>
                      </div>
                  </div>
              </div>
              <div class="row">
                    <table class="table table-bordered">
                            <thead>
                                <th>Name</th>
                                <th>Score</th>
                                <th>Goal</th>
                            </thead>
                            @foreach ($clinicals as $clinical)
                                <tr>
                                    <td class="fw-bold" style="vertical-align: middle;">{{$clinical->name}}</td>
                                    <td> <input type="text" class="form-control number_only" id="clinical_{{$clinical->id}}" name="clinical_{{$clinical->id}}"> </td>
                                    <td> <input type="text" class="form-control number_only" id="goal_{{$clinical->id}}" name="goal_{{$clinical->id}}"> </td>
                                </tr>
                            @endforeach
                    </table>
              </div>
          </div>
      </form>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_btn" onclick="SaveNewReport()">Save</button>
      </div>
    </div>
  </div>
</div>
<script>


  function ShowAddReportModal () {
    $('#add_report_modal').modal('show');
    $( '#store' ).select2( {
      theme: "bootstrap-5",
      width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
      placeholder: $( this ).data( 'placeholder' ),
      closeOnSelect: true,
      dropdownParent: $('#add_report_modal'),
		});

    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: "GET",
        url: "/admin/monthly_report/get_stores",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function(data) {
            console.log(data);
          var len = data.data.length;
          $("#store").empty();
          for( var b = 0; b<len; b++){
              var name = data.data[b].name;
              var id = data.data[b].id
              $("#store").append("<option value='"+id+"'>"+name+"</option>");
          }
      },
      error: function(msg) {
        handleErrorResponse(msg);
      }
    });
    //console.log('fire');
  }

  function SaveNewReport() {
    
    $('.alert').remove();
    $('.error_txt').remove();
    $("#save_btn").val('Saving... please wait!');
    $("#save_btn").attr('disabled', 'disabled');

    //Magic: maps all the inputs data
    var data = {};
    $('#AddForm input, textarea, select').each(function() {
      data[this.id] = this.value;
    });



  //console.log(data);
    $.ajax({
      //laravel requires this thing, it fetches it from the meta up in the head
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: "POST",
      url: "/admin/monthly_report/add_report",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function(msg) {
         $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');

        if (msg.errors) {
          $.each(msg.errors, function(key, val) {
            $("#" + key).after('<small class="error_txt">' + val[0] + '</small>');
          });
         

        } else {
          console.log(msg.responseText);
                window.location.href = '/admin/division3/monthly_report/' + $('#report_year').val()+'/'+$('#store').val();
            }
      },
      error: function(msg) {
        handleErrorResponse(msg);
         $("#save_btn").val('Save');
          $("#save_btn").removeAttr('disabled');
        //general error
        console.log("Error");
        console.log(msg.responseText);
      }
    });
  }



</script>