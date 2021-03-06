@extends('layout.main')

@section('crumb')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href=""><i class="material-icons"></i> {{ __(' Home') }} </a></li>
    </ol>
</nav>

@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="ms-panel">
            <div class="ms-panel-header d-flex justify-content-between">
                <h6>Containers</h6>
                <a href="#" class="btn btn-dark" data-toggle="modal" data-target="#addSubCat"> add new </a>
            </div>
            <div class="ms-panel-body">
                <div class="table-responsive">
                    <table id="courseEval" class="dattable table table-striped thead-dark  w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                               
                                <th>Containers Type</th>
                                <th>Containers Size</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $index => $row)
                            <tr>
                                <td>{{$index+1}}</td>
                              
                                <td>{{$row->container_type}}</td>
                                <td>{{$row->container_size}}</td>
                                <td>{{$row->container_note}}</td>
                                <td>
                                <a href="{{ route('container.edit',$row->id) }}" class="btn btn-info d-inline-block">edit</a>
                                    <a href="#" onclick="destroy('this container','{{$row->id}}')" class="btn d-inline-block btn-danger">delete</a>
                                    <form id="delete_{{$row->id}}" action="{{ route('container.destroy', $row->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" value=""></button>
                                    </form>	  </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->


</div>
@endsection

@section('modal')

<!-- Add new Modal -->
<div class="modal fade" id="addSubCat" tabindex="-1" role="dialog" aria-labelledby="addSubCat">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">X

            </button>
            <h3>Add Containers</h3>
            <div class="modal-body">
                <div class="ms-auth-container row no-gutters">
                    <div class="col-12 p-3">
                    <form action="{{route('container.store')}}" method="POST" >
                            {{ csrf_field() }}
                            <div class="ms-auth-container row">
                            <!-- <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="exampleInputPassword1" for="exampleCheck1">Containers Name</label>
                                        <input type="text" name="container_name" class="form-control" placeholder="container Name">
                                    </div>
                                </div> -->

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="exampleInputPassword1" for="exampleCheck1">Containers Type</label>
                                        <input type="text" name="container_type" class="form-control" placeholder="Containers Type">
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="exampleInputPassword1" for="exampleCheck1">Containers Size</label>
                                        <input type="text" name="container_size" class="form-control" placeholder="Containers Size">
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="exampleInputPassword1" for="exampleCheck1">Notes</label>
                                        <textarea id="newClint" name="container_note" class="form-control" placeholder="Notes" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group d-flex justify-content-end text-center">
                                <input type="button" value="Cancel" class="btn btn-dark mx-2" data-dismiss="modal" aria-label="Close">
                                <input type="submit" value="Add" class="btn btn-success ">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- /Add new Modal -->
@endsection