@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body bg-white py-4">
                <h4 >Users List</h4>
                <div class="my-2 d-flex justify-content-end">
                  <a class="btn btn-dark" href="{{route('user.create')}}">Create User</a>
                </div>
                <table class="table table-bordered" id="datatable" style="margin-top: 13px !important">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection



@push('scripts')
      <script type="text/javascript" async>
     
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
              responsive: true,
              processing: true,
              serverSide: true,
              stateSave: true,
              ajax: `{{ route('user.index') }}`,
              columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name'},
                {data: 'username'},
                {data: 'email'},
                {data: 'action'},
              ]
            });

        });

      </script>
@endpush
