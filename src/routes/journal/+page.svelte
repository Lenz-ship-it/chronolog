<script>
  import Swal from 'sweetalert2';
  import { onMount } from 'svelte';
  import { fade, fly } from 'svelte/transition';
  import { user } from '$lib/stores/user';
  import { goto } from '$app/navigation';
  import { createEntry, readEntries, deleteEntry, updateEntry, getRandomQuote } from '$lib/api';
  import { get } from 'svelte/store';
  import { base } from '$app/paths';

  import QuoteIntro from './QuoteIntro.svelte';
  import DateSelector from './DateSelector.svelte';
  import JournalHeader from './JournalHeader.svelte';
  import JournalForm from './JournalForm.svelte';
  import JournalList from './JournalList.svelte';

  // --- State ---
  let currentUser = get(user);
  let entries = [];
  let form = { feeling: '', reason: '', grateful: '', mindful: '', reflection: '' };
  let search = '';
  let fromDate = '';
  let toDate = '';
  let isEditing = false;
  let editingId = null;
  let editingDate = '';
  let showEditDateSelector = false;
  let formEl, firstInputEl;

  // Flow control
  let showIntro = true;
  let showDateSelector = false;
  let showJournal = false;

  let selectedDate = '';
  let randomQuote = { quote: '', author: '' };

  onMount(async () => {
    if (!currentUser) {
      const saved = localStorage.getItem('user');
      if (saved) {
        currentUser = JSON.parse(saved);
        user.set(currentUser);
      }
    }

    if (!currentUser) {
      goto(`${base}/login`);
      return;
    }

    try {
      randomQuote = await getRandomQuote();
    } catch {
      randomQuote = { quote: "Believe you can and you're halfway there.", author: "Theodore Roosevelt" };
    }

    await fetchEntries();
  });

  async function fetchEntries(filters = {}) {
    try {
      entries = await readEntries(filters);
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Failed to load entries',
        text: err.message || 'Please check your connection.'
      });
    }
  }

  function handleIntroContinue() {
    showIntro = false;
    setTimeout(() => (showDateSelector = true), 400);
  }

  function handleDateConfirm(date) {
    if (!date) {
      Swal.fire({
        icon: 'warning',
        title: 'No date selected',
        text: 'Please select a date first.'
      });
      return;
    }
    selectedDate = date;
    showDateSelector = false;
    setTimeout(() => (showJournal = true), 400);
  }

  // --- Journal CRUD ---
  async function saveEntry() {
    if (
      !form.feeling &&
      !form.reason &&
      !form.grateful &&
      !form.mindful &&
      !form.reflection
    ) {
      return Swal.fire({
        icon: 'warning',
        title: 'Empty Fields',
        text: 'Please fill at least one field before saving.'
      });
    }

    const payload = {
      user_id: currentUser.id,
      type: 'journal',
      date: selectedDate,
      ...form
    };

    try {
      if (isEditing && editingId) {
        await updateEntry({ id: editingId, ...payload });
        Swal.fire({
          icon: 'success',
          title: '✅ Entry Updated!',
          text: 'Your journal entry has been successfully updated.',
          timer: 1400,
          showConfirmButton: false
        });
      } else {
        await createEntry(payload);
        Swal.fire({
          icon: 'success',
          title: '✨ Entry Saved!',
          text: 'Your new journal entry has been saved.',
          timer: 1500,
          showConfirmButton: false
        });
      }

      form = { feeling: '', reason: '', grateful: '', mindful: '', reflection: '' };
      isEditing = false;
      editingId = null;
      await fetchEntries();
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: isEditing ? 'Update Failed' : 'Save Failed',
        text: err.message || 'Something went wrong.'
      });
    }
  }

  function editEntry(entry) {
    form = {
      feeling: entry.feeling || '',
      reason: entry.reason || '',
      grateful: entry.grateful || '',
      mindful: entry.mindful || '',
      reflection: entry.reflection || ''
    };

    isEditing = true;
    editingId = entry.id;
    selectedDate = entry.created_at
      ? new Date(entry.created_at).toISOString().split("T")[0]
      : '';

    Swal.fire({
      icon: 'info',
      title: '✏️ Edit Mode',
      text: 'You are now editing an existing journal entry.',
      timer: 1200,
      showConfirmButton: false
    });

    setTimeout(() => {
      if (formEl) formEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (firstInputEl) firstInputEl.focus({ preventScroll: true });
    }, 100);
  }


  function cancelEdit() {
    isEditing = false;
    editingId = null;
    editingDate = '';
    form = { feeling: '', reason: '', grateful: '', mindful: '', reflection: '' };
    Swal.fire({
      icon: 'info',
      title: 'Edit cancelled',
      text: 'You are back to creating a new journal entry.',
      timer: 1000,
      showConfirmButton: false
    });
  }

  async function removeEntry(id) {
    const confirmation = await Swal.fire({
      icon: 'warning',
      title: 'Delete Entry?',
      text: 'Are you sure you want to delete this entry?',
      showCancelButton: true,
      confirmButtonColor: '#e74c3c',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!'
    });
    if (!confirmation.isConfirmed) return;

    try {
      await deleteEntry({ id });
      await fetchEntries();
      Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: 'Your entry has been deleted.',
        timer: 1500,
        showConfirmButton: false
      });
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Delete Failed',
        text: err.message || 'Something went wrong.'
      });
    }
  }

  // --- Filtering logic ---
  let debounceTimer;
  function debounceFetch(filters) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchEntries(filters), 300);
  }

  $: if (showJournal && currentUser) {
    const filters = {
      search,
      from: fromDate,
      to: toDate
    };
    debounceFetch(filters);
  }

  function matchesFilter(entry) {
    const contentStr = [
      entry.feeling,
      entry.reason,
      entry.grateful,
      entry.mindful,
      entry.reflection
    ]
      .join(' ')
      .toLowerCase();

    const entryDate = new Date(entry.created_at).toISOString().split('T')[0];
    if (search && !contentStr.includes(search.toLowerCase())) return false;
    if (fromDate && entryDate < fromDate) return false;
    if (toDate && entryDate > toDate) return false;
    return true;
  }
</script>
<div class="p-6 bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] min-h-screen flex items-center justify-center">
  <div class="max-w-5xl w-full relative">

    {#if showIntro}
      <QuoteIntro randomQuote={randomQuote} on:click={handleIntroContinue} />
    {/if}

    {#if showDateSelector}
      <DateSelector bind:selectedDate on:confirm={e => handleDateConfirm(e.detail)} />
    {/if}

    {#if showEditDateSelector}
      <DateSelector 
        bind:selectedDate 
        on:confirm={e => {
          selectedDate = e.detail;
          showEditDateSelector = false;
        }} 
      />
    {/if}


    {#if showJournal}
      <div in:fade={{ duration: 800 }}>
        <JournalHeader {currentUser} selectedDate={selectedDate} />

        <JournalForm
          bind:form
          {isEditing}
          {editingDate}
          {selectedDate}
          {saveEntry}
          {cancelEdit}
          bind:formEl
          bind:showEditDateSelector
          bind:firstInputEl
        />

        {#if !isEditing}
          <JournalList
            {entries}
            {matchesFilter}
            {editEntry}
            {removeEntry}
            bind:search
            bind:fromDate
            bind:toDate
          />
        {/if}
      </div>
    {/if}

  </div>
</div>
