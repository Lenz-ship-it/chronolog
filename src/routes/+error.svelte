<script>
  export let status;
  export let error;
  import { goto } from '$app/navigation';
  import { base } from '$app/paths';
</script>

<div class="min-h-screen flex flex-col items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] text-center px-6">
  <div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-3xl shadow-xl p-10 max-w-lg">
    <div class="text-6xl text-[#f4a5a5] mb-5">
      <i class="fas fa-exclamation-circle"></i>
    </div>
    <h1 class="text-3xl font-bold text-[#a8e0da] mb-3">
      {status === 404 ? 'Page Not Found' : 'Something Went Wrong 😔'}
    </h1>
    <p class="text-[#5e5046] text-lg mb-8 leading-relaxed">
      {error?.message || 'We couldn’t find the page you were looking for.'}
    </p>
    <button
      on:click={() => goto(`${base}/`)}
      class="px-8 py-3 rounded-full font-bold text-white text-lg transition-all hover:scale-105"
      style="background: linear-gradient(135deg, #a8e0da 0%, #f4a5a5 100%); box-shadow: 0 4px 15px rgba(0,0,0,0.2);"
    >
      <i class="fas fa-home mr-2"></i> Go Home
    </button>
  </div>
</div>
