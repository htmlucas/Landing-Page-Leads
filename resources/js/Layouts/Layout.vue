
<template>
    <Head>
        <title>My App</title>
        <meta name="description" content="this is default description">
    </Head>
    <div>
        <header class="bg-indigo-500 text-white p-4">
            <nav class="flex items-center justify-between p-4 max-w-screen-lg mx-auto">
                <div class="space-x-6">
                    <Link :href="route('home')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'Home'}">Home</Link>
                    <Link :href="route('about')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'About'}">About</Link>
                    <Link :href="route('campaign')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'Campaign'}">Campaign</Link>
                </div>
                <div v-if="$page.props.auth.user" class="flex items-center gap-6">
                    <img :src="$page.props.auth.user.avatar ? ('storage/' + $page.props.auth.user.avatar): ('storage/avatars/default.png')" loading="lazy" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 rounded-full object-cover border-2 border-white shadow-sm">
                    <Link :href="route('dashboard')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'Dashboard'}">Dashboard</Link>
                    <Link :href="route('logout')" method="post" as="button" type="button" class="nav-link cursor-pointer">Logout</Link>
                </div>
                <div v-else class="space-x-6">                    
                    <Link :href="route('form')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'Auth/Form'}">Form</Link>
                    <Link :href="route('login')" class="nav-link" :class="{'bg-slate-700 p-2 rounded-md' : $page.component === 'Auth/Login'}">Login</Link>
                </div>
            </nav>
        </header>

        <main class="p-4">
            <slot />
        </main>
    </div>
</template>