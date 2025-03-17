@extends('errors::minimal')

@section('title', __(trans('common.http_err.404')))
@section('code', '403')
@section('message', __($exception->getMessage() ?: trans('common.http_err.403')))