<?='<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ __('site.title') }}</title>
        <link>{{ __('site.url') }}</link>
        <atom:link href="{{$atomLink}}" rel="self" type="application/rss+xml" />
        <description>{{ __('site.description') }}</description>
        <language>{{app()->currentLocale()}}</language>
        <pubDate>{{ gmdate("D, d M Y H:i:s T") }}</pubDate>
@foreach($items as $item)
        <item>
            <title>{{ $item['title'] }}</title>
            <link>{{ route('post.show', ['post' => $item['id']]) }}</link>
            <guid>{{ route('post.show', ['post' => $item['id']]) }}</guid>
            <description><![CDATA[{!! mb_substr($item['content'], 0, 150) !!}]]></description>
            <pubDate>{{ gmdate("D, d M Y H:i:s T", strtotime($item['created_at'])) }}</pubDate>
            <category>{{ $item['llm_name'] }}</category>
        </item>
@endforeach
    </channel>
</rss>