<script>
    import Swal from 'sweetalert2';
    import { user } from '$lib/stores/user';
    import { register } from '$lib/api';
    import { goto } from '$app/navigation';
    import { onMount } from 'svelte';
    import { base } from '$app/paths';
    let currentUser;
    user.subscribe(v => currentUser = v);

    onMount(() => {
        if (currentUser) goto(`${base}/journal`);
    });

    let name = '';
    let username = '';
    let password = '';
    let confirm = '';

    async function handleRegister(e) {
    e.preventDefault();
    if (password !== confirm) {
        Swal.fire({
            title: '❌ Oops!',
            text: "Password do not match!",
            icon: 'error'
        });
        return;
    }

    try {
        await register({ name, username, password });
        await Swal.fire({
            title: 'Account Created 🎉',
            text: 'Registration successful! Please log in.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        goto(`${base}/login`);
    } catch (err) {
        Swal.fire({
            title: '❌ Oops!',
            text: err.message,
            icon: 'error'
        });
    }
    }
</script>

<div class="min-h-screen flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] p-6">
  <div class="w-full max-w-md bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-8 shadow-xl">
    <div class="text-center mb-6">
      <div class="text-5xl text-[#f4a5a5] mb-3"><i class="fas fa-user-plus"></i></div>
      <h2 class="text-2xl font-bold text-[#a8e0da]">Join Us!</h2>
      <p class="text-gray-500">Create your account</p>
    </div>

    <form on:submit|preventDefault={handleRegister} class="space-y-4">
      <input bind:value={name} placeholder="Full Name" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <input bind:value={username} placeholder="Username" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <input bind:value={password} type="password" placeholder="Password" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <input bind:value={confirm} type="password" placeholder="Confirm Password" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <button class="w-full py-3 rounded-full bg-[#f4a5a5] text-white font-bold hover:bg-[#e88989] transition">
        <i class="fas fa-user-plus mr-2"></i>Register
      </button>
    </form>

    <div class="text-center mt-4 text-gray-600 text-sm">
      Already have an account? 
      <a href="{`${base}/login`}" class="text-[#a8e0da] font-bold hover:underline">Login here</a>
    </div>
  </div>
</div>
