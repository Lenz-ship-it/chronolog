<script>
  import { user } from '$lib/stores/user';
  import { base } from '$app/paths';
  import { goto } from '$app/navigation';
  export let currentUser;
</script>

<div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-5 shadow-md mb-6 flex justify-between items-center">
  <div class="flex items-center gap-4">
    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#f4a5a5] text-white font-bold text-xl">
      {currentUser?.username?.charAt(0) || 'U'}
    </div>
    <div>
      <h2 class="text-xl font-bold text-gray-700">{currentUser?.username}</h2>
      <p class="text-sm text-gray-500">Keep growing stronger 💪</p>
    </div>
  </div>
  <button
    on:click={() => {
      user.set(null);
      goto(`${base}/login`);
    }}
    class="bg-[#f4a5a5] text-white px-5 py-2 rounded-full font-semibold hover:bg-[#e88989]"
  >
    Logout
  </button>
</div>
