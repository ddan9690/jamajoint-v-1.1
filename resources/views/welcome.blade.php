@extends('layouts.guest')

@section('title', 'JamaJoint | Modern Exam Management')

@section('content')
    <section class="py-24 text-center">
        <h1 class="text-6xl font-extrabold mb-6">Stop Excel Drama.</h1>
        <p class="text-xl text-gray-600 mb-10">Real-time analysis, instant rankings, and secure access.</p>
        <a href="/login" class="bg-green-500 text-white px-10 py-4 rounded-xl text-lg hover:bg-green-600 transition">
            Start Digital Exam
        </a>
    </section>

    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-8">Why Schools are Choosing JamaJoint</h2>
            <div class="grid md:grid-cols-3 gap-8 text-left">
                <div class="p-6 bg-white rounded-xl shadow-sm">
                    <h3 class="font-bold">Instant Analysis</h3>
                    <p class="text-sm text-gray-600">No more manual formulas.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow-sm">
                    <h3 class="font-bold">Secure Access</h3>
                    <p class="text-sm text-gray-600">Only authorized teachers see data.</p>
                </div>
                <div class="p-6 bg-white rounded-xl shadow-sm">
                    <h3 class="font-bold">Mobile Ready</h3>
                    <p class="text-sm text-gray-600">Access from any device.</p>
                </div>
            </div>
        </div>
    </section>
@endsection