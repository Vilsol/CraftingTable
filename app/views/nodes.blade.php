@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'nodes'))

<script>
    function ConfirmDeleteNode(node){
        return confirm("Are you sure you want to delete the node "+node+"?");
    }
    function ConfirmDeleteAddress(address){
        return confirm("Are you sure you want to delete the public address "+address+"?");
    }
</script>

@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{{ Session::get('error') }}}</p>
    </div>
@endif

@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <p>{{{ Session::get('success') }}}</p>
    </div>
@endif

@if(Auth::user()->can('read_node'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_node'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Node
                            <small>Click to add a new node</small>
                        </a>
                    </h4>
                </div>
                <div id="collapseAdd" class="panel-collapse collapse {{ Session::has('errorAdd') ? 'in' : '' }}">
                    <div class="panel-body">
                        @if(Session::has('errorAdd'))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <ul>
                                                @foreach(Session::get('errorAdd')->all() as $errorMessage)
                                                    <li>{{ $errorMessage  }}</li>
                                                @endforeach
                                            </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => array('NodeController@postNode', true), 'class' => 'form-horizontal')) }}
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'name')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('address') != null ? 'has-error' : '' }}">
                                {{ Form::label('privateAddress-label', 'Private IP Address') }}
                                {{ Form::text('privateAddress', '', array('class'=>'form-control', 'placeholder' => '172.16.0.1')) }}
                            </div>
                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('ram') != null ? 'has-error' : '' }}">
                                {{ Form::label('ram-label', 'Memory (MB)') }}
                                {{ Form::number('ram', 1024, array('class'=>'form-control', 'min' => 1024)) }}
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Create Node', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Node::all() as $node)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $node->id }}">
                            {{{ $node->name }}}
                            <small>{{{ $node->address }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $node->id }}" class="panel-collapse collapse {{ Session::has('open'.$node->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#ip{{ $node->id }}" data-toggle="tab" style="{{ Session::has('errorIP'.$node->id) == true ? 'color:red; font-weight:bold;' : ''}}">Public IP Addresses</a></li>
                            <li role="presentation"><a href="#edit{{ $node->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$node->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="ip{{ $node->id }}">
                                @if(Session::has('errorIP'.$node->id))
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <div class="alert alert-danger alert-dismissible">
                                                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                                <ul>
                                                                                    @foreach(Session::get('errorIP'.$node->id)->all() as $errorMessage)
                                                                                        <li>{{ $errorMessage  }}</li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                <table style="margin-top: 10px" class="table table-striped table-bordered table-hover">
                                    <caption>Public IP Addresses for Bungee's to listen on.</caption>
                                    <thread>
                                        <tr>
                                            <th>IP Address<th>
                                        </tr>
                                    </thread>
                                    <tbody>
                                        @foreach($node->publicaddresses()->get() as $publicaddress)
                                            <tr>
                                                {{ Form::open(array('action' => array('NodeController@deletePAddress', $node->id, $publicaddress->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteAddress("'.$publicaddress->publicAddress.'")')) }}
                                                    <td>{{ $publicaddress->publicAddress }}</td>
                                                    <td>{{ Form::submit('Remove Address', array('class'=>'btn btn-danger')) }}</td>
                                                {{ Form::close() }}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('NodeController@postPAddress', $node->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorIP'.$node->id) && Session::get('errorIP'.$node->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('publicAddress-label', 'IP Address') }}
                                        {{ Form::text('publicAddress', '', array('class'=>'form-control', 'placeholder' => '1.1.1.1')) }}
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add IP Address', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="edit{{ $node->id }}">
                                @if(Session::has('errorEdit'.$node->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$node->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('NodeController@putNode', $node->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$node->id) && Session::get('errorEdit'.$node->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Name') }}
                                        {{ Form::text('name', $node->name, array('class'=>'form-control', 'placeholder' => 'name')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$node->id) && Session::get('errorEdit'.$node->id)->get('address') != null ? 'has-error' : '' }}">
                                        {{ Form::label('privateAddress-label', 'Private IP Address') }}
                                        {{ Form::text('privateAddress', $node->privateAddress, array('class'=>'form-control', 'placeholder' => '172.16.0.1', 'disabled')) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$node->id) && Session::get('errorEdit'.$node->id)->get('ram') != null ? 'has-error' : '' }}">
                                        {{ Form::label('ram-label', 'Memory (MB)') }}
                                        {{ Form::number('ram', $node->ram, array('class'=>'form-control', 'placeholder' => 'ram')) }}
                                    </div>
                                    @if(Auth::user()->can('update_node'))
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Save', array('class'=>'btn btn-primary')) }}
                                            </div>
                                        </div>
                                    @endif
                                {{ Form::close() }}
                                @if(Auth::user()->can('delete_node'))
                                    {{ Form::open(array('action' => array('NodeController@deleteNode', $node->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDeleteNode("'.$node->name.'")')) }}
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Delete', array('class'=>'btn btn-danger')) }}
                                            </div>
                                        </div>
                                    {{ Form::close() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@stop