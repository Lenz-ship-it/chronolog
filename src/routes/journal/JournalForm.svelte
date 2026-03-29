<script>
  export let form;
  export let isEditing;
  export let editingDate;
  export let saveEntry;
  export let cancelEdit;
  export let formEl;
  export let firstInputEl;
  export let selectedDate = '';
  export let showEditDateSelector;
</script>

<div bind:this={formEl} class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-8 shadow-md mb-8">
  <h2 class="text-2xl font-bold text-[#a8e0da] mb-4 text-center">📝 Daily Journal</h2>

  {#if isEditing}
    <div class="text-center mb-4">
      <span class="inline-block px-4 py-1 rounded-full text-sm font-semibold bg-[#fff2f2] text-[#e06b6b] border border-[#f4a5a5]">
        ✏️ Editing entry from {editingDate}
      </span>
    </div>
  {/if}

  <div class="space-y-4">
    <div>
      <h3 class="font-bold text-lg text-[#a8e0da] mb-2">How are you feeling today?</h3>
      <input bind:value={form.feeling} bind:this={firstInputEl} placeholder="Describe your feelings..." class="w-full p-3 border-2 border-[#d4c5b9] rounded-lg mb-2" />
      <input bind:value={form.reason} placeholder="What made you feel this way?" class="w-full p-3 border-2 border-[#d4c5b9] rounded-lg mb-2" />
      <input bind:value={form.grateful} placeholder="What are you grateful for today?" class="w-full p-3 border-2 border-[#d4c5b9] rounded-lg" />
    </div>

    <div>
      <h3 class="font-bold text-lg text-[#a8e0da] mb-2">The Mindful Exercise I Completed Today</h3>
      <textarea bind:value={form.mindful} placeholder="Describe..." class="w-full p-3 border-2 border-[#d4c5b9] rounded-lg min-h-[120px]"></textarea>
    </div>

    <div>
      <h3 class="font-bold text-lg text-[#a8e0da] mb-2">Today's Reflection</h3>
      <textarea bind:value={form.reflection} placeholder="Write your thoughts..." class="w-full p-3 border-2 border-[#d4c5b9] rounded-lg min-h-[120px]"></textarea>
    </div>

    <div class="text-center mt-6 space-x-4">
      <div class="flex items-center gap-4 mt-4">
        <button
          class="px-5 py-2.5 rounded-xl font-semibold text-[#5e5046] 
                bg-linear-to-r from-[#a8e0da] to-[#f4a5a5]
                shadow-md hover:shadow-lg transition-all duration-300 
                hover:scale-105"
          on:click={() => showEditDateSelector = true}
        >
          ✏️ Edit Date
        </button>

        <div class="px-4 py-2 rounded-xl bg-[#fef9f3] border-2 border-[#e8d5c4] shadow-inner">
          <p class="text-[#5e5046] font-medium text-sm">
            📅 <span class="font-semibold">{selectedDate}</span>
          </p>
        </div>

      </div>


      <button
        type="button"
        on:click={saveEntry}
        class={`px-8 py-3 mt-8 rounded-full font-bold text-lg transition text-white ${
          isEditing ? 'bg-[#f4a5a5] hover:bg-[#e88989]' : 'bg-[#a8e0da] hover:bg-[#8fccc5]'
        }`}
      >
        <i class={`fas ${isEditing ? 'fa-edit' : 'fa-save'} mr-2`}></i>
        {isEditing ? 'Update Journal Entry' : 'Save Journal Entry'}
      </button>

      {#if isEditing}
        <button
          type="button"
          on:click={cancelEdit}
          class="px-8 py-3 rounded-full font-bold text-lg text-[#5e5046] bg-[#fef9f3] border-2 border-[#e8d5c4] hover:bg-[#fff2f2] transition"
        >
          <i class="fas fa-times mr-2"></i>Cancel
        </button>
      {/if}
    </div>
  </div>
</div>
