<script>
    import Swal from 'sweetalert2';
    import { user } from '$lib/stores/user';
    import { login } from '$lib/api';
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { base } from '$app/paths';

    let username = '';
    let password = '';
    let currentUser;
    user.subscribe(v => (currentUser = v));

    onMount(() => {
        const savedUser = localStorage.getItem('user');
        const savedToken = localStorage.getItem('token');

        if (savedUser && savedToken) {
            user.set(JSON.parse(savedUser));
            goto(`${base}/journal`);
        }
    });

    async function handleLogin(e) {
        e.preventDefault();

        try {
            const res = await login({ username, password });
            localStorage.setItem('token', res.token);
            localStorage.setItem('user', JSON.stringify(res.user));

            user.set(res.user);

            await Swal.fire({
                title: 'Welcome Back! 💖',
                text: 'Login successful!',
                icon: 'success',
                timer: 1800,
                showConfirmButton: false
            });

            goto(`${base}/journal`);
        } catch (err) {
            Swal.fire({
                title: '❌ Oops!',
                text: err.message || 'Invalid credentials.',
                icon: 'error'
            });
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] p-6">
  <div class="w-full max-w-md bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-8 shadow-xl">
    <div class="text-center mb-6">
      <h2 class="text-3xl font-bold text-[#a8e0da]">Welcome Back!</h2>
      <p class="text-gray-500">Login to continue your journey</p>
    </div>

    <form on:submit|preventDefault={handleLogin} class="space-y-4">

      <label for="username" class="font-semibold text-sm text-[#85d0c8]">Username</label>
      <input
        id="username"
        bind:value={username}
        placeholder="Masukkan Username"
        required
        class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]"
      />

      <div>
        <a
          href="{`${base}/reset-password`}"
          class="text-[#f4a5a5] font-bold hover:underline block text-sm float-right mb-1"
        >
          Reset password?
        </a>

        <label for="password" class="font-semibold text-sm text-[#85d0c8]">Password</label>
        <input
          id="password"
          bind:value={password}
          type="password"
          placeholder="Masukkan Password"
          required
          class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]"
        />
      </div>

      <button class="w-full py-3 rounded-full bg-[#a8e0da] text-white font-bold hover:bg-[#8fccc5] transition">
        <i class="fas fa-sign-in-alt mr-2"></i>Login
      </button>

    </form>


    <div class="text-center mt-4 text-gray-600 text-sm">
      Don't have an account? 
      <a href="{`${base}/register`}" class="text-[#a8e0da] font-bold hover:underline">Register here</a>
    </div>
  </div>
</div>
