@extends('layouts.master')

@section('content')
@include('navbars.topnav', array('navBarPage'=>'networks'))

<script>
    function ConfirmDeleteServerType(servertype){
            return confirm("Are you sure you want to delete the server type "+servertype+"?");
    }

    function ConfirmDeleteNode(node){
            return confirm("Are you sure you want to delete the node "+node+"?");
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

@if(Auth::user()->can('read_network'))
    <div class="panel-group" id="accordion">
        @if(Auth::user()->can('create_network'))
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Network
                            <small>Click to add a new network</small>
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
                                                    <li>{{{ $errorMessage  }}}</li>
                                                @endforeach
                                            </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ Form::open(array('action' => 'NetworkController@postNetwork', 'class' => 'form-horizontal')) }}

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('name') != null ? 'has-error' : '' }}">
                                {{ Form::label('name-label', 'Network Name') }}
                                {{ Form::text('name', '', array('class'=>'form-control', 'placeholder' => 'i.e My Network', 'maxlength' => '100')) }}
                            </div>

                            <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorAdd') && Session::get('errorAdd')->get('description') != null ? 'has-error' : '' }}">
                                {{ Form::label('description-label', 'Network Description') }}
                                {{ Form::text('description', '', array('class'=>'form-control', 'placeholder' => 'i.e This is my network', 'maxlength' => '255')) }}
                            </div>

                            <div style="margin-top:10px" class="form-group">
                                <div class="col-md-12">
                                    {{ Form::submit('Add Network', array('class'=>'btn btn-success')) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @endif
        @foreach(Network::all() as $network)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $network->id }}">
                            {{{ $network->name }}}
                            <small>{{{ $network->description }}}</small>
                        </a>
                    </h4>
                </div>
                <div id="collapse{{ $network->id }}" class="panel-collapse collapse {{ Session::has('open'.$network->id) ? 'in' : '' }}">
                    <div class="panel-body">
                        @if($network->overProvisioned() == true)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>This network is currently over provisioned. Please consider adding more nodes or decrease the amount of servers for optimal performance.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($network->defaultServerType() == null)
                            <div class="row">
                                <div class="col-sm-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>There is currently no default server type. This network will not function correctly without a default server type.</p>
                                </div>
                                </div>
                            </div>
                        @endif
                        @if($network->hasBungee() == false)
                            <div class="row">
                                <div class="col-sm-12">
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <p>This network is currently unjoinable. Please add a node with a bungee type.</p>
                                </div>
                                </div>
                            </div>
                        @endif
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#stats{{ $network->id }}" data-toggle="tab">Stats</a></li>
                            <li role="presentation"><a href="#servertypes{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddServerType'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Server Types</a></li>
                            <li role="presentation"><a href="#nodes{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorAddNode'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Nodes</a></li>
                            <li role="presentation"><a href="#edit{{ $network->id }}" data-toggle="tab" style="{{ Session::has('errorEdit'.$network->id) == true ? 'color:red; font-weight:bold;' : ''}}">Edit</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="stats{{ $network->id }}">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <h4 class="text-center">Online Players</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getOnlinePlayers() }} / {{ $network->getTotalPlayers() }}</span></p>
                                    </div>
                                    <div class="col-xs-3">
                                        <h4 class="text-center">Server Memory Usage</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getUsedServerRam() }} MB / {{ $network->getTotalServerRam() }} MB</span></p>
                                    </div>
                                    <div class="col-xs-3">
                                        <h4 class="text-center">Bungee Memory Usage</h4>
                                        <p class="text-center"><span class="text-muted">{{ $network->getUsedBungeeRam() }} MB / {{ $network->getTotalBungeeRam() }} MB</span></p>
                                    </div>
                                    <div class="col-xs-3">
                                        <h4 class="text-center">Something else</h4>
                                        <p class="text-center"><span class="text-muted">something</span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4>Online Bungees</h4>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thread>
                                                <tr>
                                                    <td>Bungee Type</td>
                                                    <td>Node</td>
                                                    <td>Public IP Address</td>
                                                    <td>Manage</td>
                                                </tr>
                                            </thread>
                                            <tbody>
                                                @foreach($network->bungees()->get()->all() as $bungee)
                                                    <tr>
                                                        <td>{{ $bungee->bungeetype()->name }}</td>
                                                        <td>{{ $bungee->node()->name }}</td>
                                                        <td>{{ $bungee->publicaddress()->publicAddress }}</td>
                                                        <td>Button</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <h4>Online Servers</h4>
                                        <table class="table table-striped table-bordered table-hover">
                                            <thread>
                                                <tr>
                                                    <td>Server Type</td>
                                                    <td>Server Number</td>
                                                    <td>Node</td>
                                                    <td>Port</td>
                                                    <td>Manage</td>
                                                </tr>
                                            </thread>
                                            <tbody>
                                                @foreach($network->servers()->get()->all() as $server)
                                                    <tr>
                                                        <td>{{{ $server->servertype()->name }}}</td>
                                                        <td>{{{ $server->number }}}</td>
                                                        <td>{{{ $server->node()->name }}}</td>
                                                        <td>{{{ $server->port }}}</td>
                                                        <td>Button</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="servertypes{{ $network->id }}">
                                @if(Session::has('errorAddServerType'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddServerType'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <table style="margin-top: 10px" class="table table-striped table-bordered table-hover">
                                    <thread>
                                        <tr>
                                            <td>Server Type Name</td>
                                            <td>Amount</td>
                                            <td>Default</td>
                                        </tr>
                                    </thread>
                                    <tbody>
                                        @foreach($network->servertypes()->get() as $servertype)
                                            <tr>
                                                {{ Form::open(array('action' => array('NetworkController@deleteServerType', $network->id, $servertype->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteServerType("'.$servertype->servertype()->name.'")')) }}
                                                    <td>{{{ $servertype->servertype()->name }}}</td>
                                                    <td>{{{ $servertype->amount }}}</td>
                                                    <td>{{ $servertype->defaultServerType ? 'Yes' : 'No' }}</td>
                                                    <td>{{ Form::submit('Remove Server Type', array('class'=>'btn btn-danger')) }}</td>
                                                {{ Form::close() }}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('NetworkController@postServerType', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('servertype-label', 'Server Type Name') }}
                                        <select name='servertype' class="form-control" id="servertypeList">
                                            <option selected value="-1">Please select a server type</option>
                                            @foreach(ServerType::all() as $servertype)
                                                <option value="{{ $servertype->id }}">{{{ $servertype->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="margin-top:10px" class="input-group">
                                        {{ Form::label('amount-label', 'Server Type Amount') }}
                                        {{ Form::number('amount', 1, array('class' => 'form-control', 'min' => 1)) }}
                                    </div>
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('default-label', 'Default Server Type') }}
                                        {{ Form::checkbox('default', '1', false, array('class'=>'form-control')) }}
                                    </div>
                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Server Type', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="nodes{{ $network->id }}">
                                @if(Session::has('errorAddNode'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorAddNode'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <table style="margin-top: 10px" class="table table-striped table-bordered table-hover">
                                    <thread>
                                        <tr>
                                            <td>Node Name</td>
                                            <td>Bungee Name</td>
                                            <td>Public IP Address</td>
                                        </tr>
                                    </thread>
                                    <tbody>
                                        @foreach($network->nodes()->get() as $node)
                                            <tr>
                                                {{ Form::open(array('action' => array('NetworkController@deleteNode', $network->id, $node->id), 'class' => 'form-horizontal', 'method' => 'DELETE', 'onsubmit' => 'return ConfirmDeleteNode("'.$node->node()->name.'")')) }}
                                                    <td>{{{ $node->node()->name }}}</td>
                                                    <td>{{{ $node->bungeetype() != null ? $node->bungeetype()->name : '' }}}</td>
                                                    <td>{{{ $node->publicaddress() != null ? $node->publicaddress()->publicAddress : '' }}}</td>
                                                    <td>{{ Form::submit('Remove Node', array('class'=>'btn btn-danger')) }}</td>
                                                {{ Form::close() }}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ Form::open(array('action' => array('NetworkController@postNode', $network->id), 'class' => 'form-horizontal', 'method' => 'POST')) }}
                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('node-label', 'Node Name') }}
                                        <select name='node' class="form-control nodeList" id="{{$network->id}}">
                                            <option selected value="-1">Please select a node</option>
                                            @foreach(Node::all() as $node)
                                                <option value="{{ $node->id }}">{{{ $node->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group">
                                        {{ Form::label('bungeetype-label', 'Optional Bungee Type') }}
                                        <select name='bungeetype' class="form-control" id="servertypeList">
                                            <option selected value="-1">Please select a bungee type</option>
                                            @foreach(BungeeType::all() as $bungee)
                                                <option value="{{ $bungee->id }}">{{{ $bungee->name }}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="margin-top:10px" class="form-group">
                                        <div class="col-md-12">
                                            {{ Form::submit('Add Node', array('class'=>'btn btn-primary')) }}
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <div class="tab-pane" id="edit{{ $network->id }}">
                                @if(Session::has('errorEdit'.$network->id))
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <ul>
                                                    @foreach(Session::get('errorEdit'.$network->id)->all() as $errorMessage)
                                                        <li>{{ $errorMessage  }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{ Form::open(array('action' => array('NetworkController@putNetwork', $network->id), 'class' => 'form-horizontal', 'method' => 'PUT')) }}

                                    <div style="margin-top: 15px; margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$network->id) && Session::get('errorEdit'.$network->id)->get('name') != null ? 'has-error' : '' }}">
                                        {{ Form::label('name-label', 'Network Name') }}
                                        {{ Form::text('name', $network->name, array('class'=>'form-control', 'placeholder' => 'i.e My Network', 'maxlength' => '100')) }}
                                    </div>

                                    <div style="margin-bottom: 25px" class="input-group {{ Session::has('errorEdit'.$network->id) && Session::get('errorEdit'.$network->id)->get('description') != null ? 'has-error' : '' }}">
                                        {{ Form::label('description-label', 'Network Description') }}
                                        {{ Form::text('description', $network->description, array('class'=>'form-control', 'placeholder' => 'i.e This is my network', 'maxlength' => '255')) }}
                                    </div>

                                    @if(Auth::user()->can('update_network'))
                                        <div style="margin-top:10px" class="form-group">
                                            <div class="col-md-12">
                                                {{ Form::submit('Save Network', array('class'=>'btn btn-primary')) }}
                                            </div>
                                        </div>
                                    @endif

                                {{ Form::close() }}
                                <script>
                                    function ConfirmDelete(){
                                        return confirm("Are you sure you want to delete the network {{{ $network->name }}}?");
                                    }
                                </script>
                                @if(Auth::user()->can('delete_network'))
                                    {{ Form::open(array('action' => array('NetworkController@deleteNetwork', $network->id), 'class' => 'form-horizontal', 'method'=>'DELETE', 'onsubmit' => 'return ConfirmDelete()')) }}
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