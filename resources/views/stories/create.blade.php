<x-layout title="Submit">
    <x-errors />

    <form method="POST" action="{{ route('stories.store') }}" class="submitform">
        @csrf
        <table>
            <tr><td>title</td><td><input type="text" name="title" value="{{ old('title') }}" size="50" maxlength="80" autofocus></td></tr>
            <tr><td>url</td><td><input type="url" name="url" value="{{ old('url') }}" size="50"></td></tr>
            <tr><td>text</td><td><textarea name="text" rows="4" cols="49">{{ old('text') }}</textarea></td></tr>
            <tr><td>province</td><td>
                <select name="province">
                    <option value="">National</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->value }}" @selected(old('province') === $province->value)>{{ $province->label() }}</option>
                    @endforeach
                </select>
            </td></tr>
            <tr><td></td><td><label><input type="checkbox" name="is_political" value="1" @checked(old('is_political'))> politics</label></td></tr>
            <tr><td></td><td><input type="submit" value="submit"></td></tr>
            <tr><td></td><td class="subtext">Leave url blank to submit a question for discussion. If there is no url, text will appear at the top of the thread. If there is a url, text is optional.</td></tr>
        </table>
    </form>
</x-layout>
