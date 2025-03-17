@extends('errors::minimal')

@section('title', __(trans('common.http_err.404')))
@section('code', '404')
@section('message', __(($exception->getMessage() && ($custom_message ?? false)) ? $exception->getMessage() : trans('common.http_err.404')))
