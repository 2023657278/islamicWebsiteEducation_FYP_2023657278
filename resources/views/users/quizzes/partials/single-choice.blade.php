<div class="space-y-4">
    @foreach($options as $option)
    <label class="block group cursor-pointer">
        <input type="radio" name="answer" value="{{ $option->id }}" class="hidden peer" required>
        <div class="w-full p-4 bg-white/5 border border-white/10 rounded-xl transition-all duration-300 peer-checked:bg-cyan-500/20 peer-checked:border-cyan-400 peer-checked:shadow-[0_0_15px_rgba(34,211,238,0.3)] group-hover:bg-white/10">
            <div class="flex items-center">
                <div class="w-6 h-6 rounded-full border-2 border-white/20 flex items-center justify-center peer-checked:border-cyan-400 mr-4">
                    <div class="w-2 h-2 rounded-full bg-cyan-400 opacity-0 transition-opacity peer-checked:opacity-100 shadow-[0_0_8px_#22d3ee]"></div>
                </div>
                <span class="text-white/80 group-hover:text-white transition-colors">{{ $option->option_text }}</span>
            </div>
        </div>
    </label>
    @endforeach
</div>