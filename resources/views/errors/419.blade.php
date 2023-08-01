@php
    $headers = $exception->getHeaders();
@endphp

@extends('errors::minimal')

@section('title', __('Your session expired'))
@unless (isset($headers['hide_http_code']))
    @section('code', '419')
@endunless
@if ($exception instanceof \Illuminate\Session\TokenMismatchException)
    @section('message', __('message.notify.error.419'))
@else
    @section('message', $exception->getMessage() ?: __('Your session expired'))
@endif
