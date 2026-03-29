<script>
  import { createEventDispatcher } from 'svelte';
  import { fade, fly } from 'svelte/transition';
  export let randomQuote;
  const dispatch = createEventDispatcher();
</script>

<div
  in:fade={{ duration: 600 }}
  out:fly={{ y: -60, duration: 800 }}
  class="fixed inset-0 flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] z-50 overflow-hidden"
>
  <div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-3xl shadow-xl text-center p-10 max-w-xl mx-auto transform transition-transform duration-700 ease-out hover:scale-[1.02]">
    <div class="text-5xl text-[#f4a5a5] mb-5">
      <i class="fas fa-quote-left"></i>
    </div>
    <p class="text-2xl text-[#5e5046] italic leading-relaxed mb-4">"{randomQuote.quote}"</p>
    <p class="text-[#a8e0da] font-semibold text-lg mb-8">— {randomQuote.author}</p>
    <button
      on:click={() => dispatch('click')}
      class="px-8 py-3 rounded-full font-bold text-white text-lg transition-all hover:scale-105 cursor-pointer"
      style="background: linear-gradient(135deg, #a8e0da 0%, #f4a5a5 100%); box-shadow: 0 4px 15px rgba(0,0,0,0.2);"
    >
      <i class="fas fa-rocket mr-2"></i>Start My Journey
    </button>
  </div>
</div>
