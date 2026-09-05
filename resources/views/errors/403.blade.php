@extends('errors.layout')

@section('code', '403')
@section('title', 'Access Forbidden')
@section('message', $exception->getMessage() ?: 'Your user role or assigned permissions do not allow access to this module.')
