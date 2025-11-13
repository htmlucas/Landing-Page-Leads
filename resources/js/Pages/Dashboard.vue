<template>
    <Head title="Dashboard" />
    <div class="">
        <p class="p-4 bg-green-200">{{ $page.props.flash.greet }}</p>
        <h1>
            Welcome to the Dashboard! {{ $page.props.auth.user.name }}
        </h1>
    </div>
</template>