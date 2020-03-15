@extends('errors::layout')
{{-- @extends('layouts.main_nonav') --}}

@section('title', 'Service Unavailable')

@section('message', $exception->getMessage())