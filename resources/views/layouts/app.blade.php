@php
    $theme = auth()->check() ? auth()->user()->theme : 'sneat';
    // Fallback if theme is missing or null
    if (!$theme || !in_array($theme, ['sneat'])) $theme = 'sneat';
@endphp

@extends('layouts.themes.' . $theme)
