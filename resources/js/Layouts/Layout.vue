
<template>
    <Head>
        <title>My App</title>
        <meta name="description" content="this is default description">
    </Head>
    <div>
        <header class="bg-indigo-500 text-white p-4">
            <nav class="flex items-center justify-between p-4 max-w-screen-lg mx-auto">
                <div class="space-x-6">
                    <Link :href="route('home')" class="nav-link">Home</Link>
                    <Link :href="route('about')" class="nav-link">About</Link>                    
                </div>
                <div class="space-x-6">                    
                    <Link :href="route('form')" class="nav-link">Form</Link>
                    <Link :href="route('login')" class="nav-link">Login</Link>
                </div>
            </nav>
        </header>

        <main class="p-4">
            <slot />
        </main>
    </div>
</template>