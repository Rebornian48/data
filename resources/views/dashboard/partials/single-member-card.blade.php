<a href="{{ route('members.show', $member) }}"
   class="bg-white dark:bg-slate-800 rounded-xl border {{ $isCenter ? 'border-brand ring-2 ring-brand/30' : 'border-slate-200 dark:border-slate-700' }} overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition">
    <div class="aspect-square bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center relative">
        @if ($isCenter)
            <span class="absolute top-2 left-2 text-[10px] font-bold bg-brand text-white px-2 py-0.5 rounded-full uppercase tracking-wide z-10">
                Center
            </span>
        @endif
        @if ($member->photo_url)
            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-20 h-20 gradient-brand rounded-full flex items-center justify-center text-white text-2xl font-bold">
                {{ strtoupper(substr($member->nickname ?: $member->name, 0, 1)) }}
            </div>
        @endif
    </div>
    <div class="p-3">
        <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm truncate">{{ $member->name }}</div>
        @if ($member->nickname)
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $member->nickname }}</div>
        @endif
        @if ($member->generation)
            <div class="mt-2">
                <span class="text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded">
                    {{ $member->generation->code }}
                </span>
            </div>
        @endif
    </div>
</a>
