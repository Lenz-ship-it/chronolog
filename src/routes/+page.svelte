<script>
  import { user } from '$lib/stores/user';
  import { goto } from '$app/navigation';
  import { onMount, onDestroy } from 'svelte';
  import { base } from '$app/paths';

  let currentUser;
  const unsub = user.subscribe(v => currentUser = v);

  onMount(() => {
    setTimeout(() => {
      if (currentUser) goto(`${base}/journal`)
      else goto(`${base}/login`);
    }, 500);
  });
  onDestroy(() => unsub());


</script>

<div class="min-h-screen relative flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] text-center">
  <div>
    <h2 class="text-3xl font-bold text-[#f4a5a5] mb-4">Loading...</h2>
    <p class="text-gray-600">Please wait while we redirect you 💫</p>
  </div>
</div>
