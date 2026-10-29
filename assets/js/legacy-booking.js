const typeSelect = document.getElementById('appointment_type');
const categorySelect = document.getElementById('category_select');
const providerSelect = document.getElementById('provider_select');
const providerId = document.getElementById('provider_id');
const providerName = document.getElementById('provider_name');
const categoryHidden = document.getElementById('category');
const qualification = document.getElementById('qualification');
const specialty = document.getElementById('specialty');
const serviceName = document.getElementById('service_name');
const providerInfo = document.getElementById('providerInfo');
const timeSelect = document.getElementById('time');
const slotPreview = document.getElementById('slotPreview');
const dateInput = document.getElementById('date');

const doctors = window.mediBookDoctors || [];
const services = window.mediBookServices || [];
const slots = window.mediBookSlots || {};
const booked = window.mediBookBooked || {};

function sourceItems() {
    return typeSelect?.value === 'service' ? services : doctors;
}

function getCategories() {
    const groups = {};
    sourceItems().forEach(item => {
        const category = item.category || 'Other';
        if (!groups[category]) groups[category] = [];
        groups[category].push(item);
    });
    return groups;
}

function resetSelect(select, placeholder, disabled = false) {
    if (!select) return;
    select.innerHTML = `<option value="">${placeholder}</option>`;
    select.disabled = disabled;
}

function updateCategories() {
    resetSelect(categorySelect, '-- Select appointment type first --', !typeSelect?.value);
    resetSelect(providerSelect, '-- Select category first --', true);
    if (timeSelect) timeSelect.innerHTML = '<option value="">-- Select provider first --</option>';
    if (slotPreview) slotPreview.innerHTML = '';
    if (!typeSelect?.value) return;

    const groups = getCategories();
    Object.keys(groups).sort().forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = `${category} (${groups[category].length})`;
        categorySelect.appendChild(option);
    });
    categorySelect.disabled = false;
}

function updateProviders() {
    resetSelect(providerSelect, '-- Select doctor/service --', !categorySelect?.value);
    if (timeSelect) timeSelect.innerHTML = '<option value="">-- Select provider first --</option>';
    if (slotPreview) slotPreview.innerHTML = '';
    if (!categorySelect?.value) return;

    const items = sourceItems().filter(item => item.category === categorySelect.value);
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = typeSelect.value === 'doctor' ? `${item.name} — ${item.specialty}` : item.name;
        providerSelect.appendChild(option);
    });
    providerSelect.disabled = false;
}

function selectedItem() {
    return sourceItems().find(item => item.id === providerSelect?.value) || null;
}

function updateProviderInfo() {
    const item = selectedItem();
    if (!item) {
        if (providerInfo) providerInfo.textContent = 'Your selected doctor or service information will appear here.';
        if (providerId) providerId.value = '';
        if (providerName) providerName.value = '';
        if (categoryHidden) categoryHidden.value = '';
        if (qualification) qualification.value = '';
        if (specialty) specialty.value = '';
        if (serviceName) serviceName.value = '';
        updateTimeSlots();
        return;
    }

    providerId.value = item.id;
    providerName.value = item.name;
    categoryHidden.value = item.category || '';
    qualification.value = item.qualification || '';
    specialty.value = item.specialty || '';
    serviceName.value = typeSelect.value === 'service' ? item.name : '';

    if (providerInfo) {
        providerInfo.textContent = typeSelect.value === 'doctor'
            ? `${item.qualification} • ${item.specialty}`
            : `${item.description} • ${item.duration}`;
    }
    updateTimeSlots();
}

function updateTimeSlots() {
    if (!timeSelect || !providerSelect) return;
    const item = selectedItem();
    timeSelect.innerHTML = '<option value="">-- Select an available time --</option>';
    if (!item) {
        if (slotPreview) slotPreview.innerHTML = '';
        return;
    }

    const key = `${typeSelect.value}|${item.id}`;
    const providerSlots = slots[key] || [];
    const selectedDate = dateInput?.value || '';

    providerSlots.forEach(slot => {
        const option = document.createElement('option');
        const bookingKey = `${typeSelect.value}|${item.id}|${selectedDate}|${slot.slot_time}`;
        const isBooked = Boolean(booked[bookingKey]);
        const isOffline = slot.slot_status === 'Offline Visit';
        const isBlocked = slot.slot_status === 'Blocked';
        const unavailable = isOffline || isBlocked || isBooked;

        option.value = slot.slot_time;
        if (isBooked) option.textContent = `${slot.slot_time} — Booked`;
        else if (isOffline) option.textContent = `${slot.slot_time} — Offline Visitor`;
        else if (isBlocked) option.textContent = `${slot.slot_time} — Blocked`;
        else option.textContent = `${slot.slot_time} — Token ${slot.token_no ?? ''}`;
        option.disabled = unavailable;
        timeSelect.appendChild(option);
    });

    if (slotPreview) {
        slotPreview.innerHTML = providerSlots.map(slot => {
            const bookingKey = `${typeSelect.value}|${item.id}|${selectedDate}|${slot.slot_time}`;
            const isBooked = Boolean(booked[bookingKey]);
            const status = isBooked ? 'Booked' : slot.slot_status;
            const cls = status.toLowerCase().replaceAll(' ', '-');
            const tokenLabel = slot.token_no ? ` • Token ${slot.token_no}` : '';
            return `<span class="slot-chip ${cls}">${slot.slot_time}<b>${status}${tokenLabel}</b></span>`;
        }).join('');
    }
}

function chooseProvider(type, id) {
    if (!typeSelect || !categorySelect || !providerSelect) return;
    typeSelect.value = type;
    updateCategories();
    const item = sourceItems().find(x => x.id === id);
    if (!item) return;
    categorySelect.value = item.category;
    updateProviders();
    providerSelect.value = id;
    updateProviderInfo();
    document.getElementById('booking')?.scrollIntoView({ behavior: 'smooth' });
}

typeSelect?.addEventListener('change', updateCategories);
categorySelect?.addEventListener('change', updateProviders);
providerSelect?.addEventListener('change', updateProviderInfo);
dateInput?.addEventListener('change', updateTimeSlots);

document.querySelectorAll('.doctor-book').forEach(button => {
    button.addEventListener('click', () => chooseProvider('doctor', button.dataset.id));
});
document.querySelectorAll('.service-book').forEach(button => {
    button.addEventListener('click', () => chooseProvider('service', button.dataset.id));
});

if (dateInput) {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    dateInput.min = `${yyyy}-${mm}-${dd}`;
}

updateCategories();
