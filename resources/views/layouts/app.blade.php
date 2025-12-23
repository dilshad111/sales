@php
    $theme = auth()->check() ? auth()->user()->theme : 'sneat';
    // Fallback if theme index is missing or null
    if (!$theme) $theme = 'sneat';
@endphp

@extends('layouts.themes.' . $theme)
