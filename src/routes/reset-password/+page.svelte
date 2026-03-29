<script>
    import Swal from 'sweetalert2';
    import { resetPassword } from '$lib/api';
    import { goto } from '$app/navigation';
    import { base } from '$app/paths';

    let username = '';
    let password = '';
    let confirm = '';

    async function handleReset(e) {
        e.preventDefault();
        if (password !== confirm) {
            Swal.fire({ title: '❌ Failed', text: 'Password do not match', icon: 'error' });
            return;
        }

        try {
            await resetPassword({ username, password });
            await Swal.fire({ title: 'Success 🎉', text: 'Password updated!', icon: 'success' });
            goto(`${base}/login`);
        } catch (err) {
            Swal.fire({ title: '❌ Error', text: err.message, icon: 'error' });
        }
    }
</script>

<div class="min-h-screen flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] p-6">
  <div class="w-full max-w-md bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-8 shadow-xl">
    <div class="text-center mb-6">
      <div class="text-5xl text-[#f4a5a5] mb-3"><i class="fas fa-unlock-alt"></i></div>
      <h2 class="text-2xl font-bold text-[#a8e0da]">Reset Password</h2>
      <p class="text-gray-500">Enter your username and new password</p>
    </div>

    <form on:submit|preventDefault={handleReset} class="space-y-4">
      <input bind:value={username} placeholder="Username" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <input bind:value={password} type="password" placeholder="New Password" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <input bind:value={confirm} type="password" placeholder="Confirm New Password" required class="w-full p-3 rounded-lg border-2 border-[#d4c5b9]" />
      <button class="w-full py-3 rounded-full bg-[#f4a5a5] text-white font-bold hover:bg-[#e88989] transition">
        <i class="fas fa-unlock mr-2"></i>Reset Password
      </button>
    </form>

    <div class="text-center mt-4 text-gray-600 text-sm">
      Remember your password?
      <a href="{`${base}/login`}" class="text-[#a8e0da] font-bold hover:underline">Login here</a>
    </div>
  </div>
</div>
