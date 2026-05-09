<div class="space-y-4">
    @foreach($options as $option)
    <label class="block group cursor-pointer">
        <input type="checkbox" name="answers[]" value="{{ $option->id }}" class="hidden peer">
        <div class="w-full p-4 bg-white/5 border border-white/10 rounded-xl transition-all duration-300 peer-checked:bg-purple-500/20 peer-checked:border-purple-400 peer-checked:shadow-[0_0_15px_rgba(192,132,252,0.3)] group-hover:bg-white/10">
            <div class="flex items-center">
                <div class="w-6 h-6 rounded border-2 border-white/20 flex items-center justify-center peer-checked:border-purple-400 mr-4">
                    <i class="fas fa-check text-xs text-purple-400 opacity-0 peer-checked:opacity-100"></i>
                </div>
                <span class="text-white/80 group-hover:text-white transition-colors">{{ $option->option_text }}</span>
            </div>
        </div>
    </label>
    @endforeach
</div>