@extends('layouts.app')

@section('title', 'Hướng dẫn SSO - Session Sharing')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Hướng dẫn SSO Session Sharing</h1>
        <p class="text-gray-600">Tài liệu hướng dẫn chi tiết về hệ thống Single Sign-On</p>
    </div>

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Quay lại trang chủ
        </a>
    </div>

    <!-- README Content -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="prose prose-lg max-w-none">
            @php
                // Convert markdown to HTML with enhanced styling
                $html = $readmeContent;
                
                // Headers with icons and better styling
                $html = preg_replace('/^# (.+)$/m', '<h1 class="text-3xl font-bold text-gray-900 mb-6 mt-8 flex items-center border-b-2 border-blue-200 pb-3"><span class="mr-3">🔐</span>$1</h1>', $html);
                $html = preg_replace('/^## (.+)$/m', '<h2 class="text-2xl font-bold text-gray-900 mb-4 mt-8 flex items-center border-b border-gray-200 pb-2"><span class="mr-2">📖</span>$1</h2>', $html);
                $html = preg_replace('/^### (.+)$/m', '<h3 class="text-xl font-bold text-gray-900 mb-3 mt-6 flex items-center"><span class="mr-2">⚙️</span>$1</h3>', $html);
                $html = preg_replace('/^#### (.+)$/m', '<h4 class="text-lg font-semibold text-gray-900 mb-2 mt-4 flex items-center"><span class="mr-2">📝</span>$1</h4>', $html);
                
                // Bold text with better styling
                $html = preg_replace('/\*\*(.+?)\*\*/', '<strong class="font-semibold text-gray-900 bg-yellow-50 px-1 rounded">$1</strong>', $html);
                
                // Italic text
                $html = preg_replace('/\*(.+?)\*/', '<em class="italic text-gray-700">$1</em>', $html);
                
                // Enhanced code blocks with syntax highlighting colors
                $html = preg_replace_callback('/```(\w+)?\n(.*?)\n```/s', function($matches) {
                    $language = $matches[1] ?: 'text';
                    $code = htmlspecialchars($matches[2]);
                    
                    // Add language-specific colors
                    $colors = [
                        'php' => 'bg-purple-50 border-purple-200 text-purple-800',
                        'bash' => 'bg-green-50 border-green-200 text-green-800',
                        'env' => 'bg-blue-50 border-blue-200 text-blue-800',
                        'sql' => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                        'text' => 'bg-gray-50 border-gray-200 text-gray-800'
                    ];
                    
                    $colorClass = $colors[$language] ?? $colors['text'];
                    
                    return '<div class="code-block my-6 rounded-lg border-2 ' . $colorClass . ' overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-100 border-b">
                            <span class="text-xs font-medium text-gray-600 uppercase">' . strtoupper($language) . '</span>
                            <button onclick="copyToClipboard(this)" class="copy-btn text-xs text-gray-500 hover:text-gray-700 cursor-pointer bg-white px-2 py-1 rounded border">
                                📋 Copy
                            </button>
                        </div>
                        <pre class="p-4 overflow-x-auto"><code class="text-sm leading-relaxed">' . $code . '</code></pre>
                    </div>';
                }, $html);
                
                // Enhanced inline code
                $html = preg_replace('/`(.+?)`/', '<code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-mono border border-gray-200">$1</code>', $html);
                
                // Enhanced links with icons
                $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" class="text-blue-600 hover:text-blue-800 underline inline-flex items-center transition-colors"><span class="mr-1">🔗</span>$1</a>', $html);
                
                // Enhanced lists with better styling
                $html = preg_replace('/^\* (.+)$/m', '<li class="ml-6 mb-2 flex items-start"><span class="text-blue-500 mr-2 mt-1">•</span><span class="flex-1">$1</span></li>', $html);
                $html = preg_replace('/^- (.+)$/m', '<li class="ml-6 mb-2 flex items-start"><span class="text-red-500 mr-2 mt-1">-</span><span class="flex-1">$1</span></li>', $html);
                $html = preg_replace('/^(\d+)\. (.+)$/m', '<li class="ml-6 mb-2 flex items-start"><span class="bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center mr-3 mt-0.5 font-bold">$1</span><span class="flex-1">$2</span></li>', $html);
                
                // Enhanced horizontal rules
                $html = preg_replace('/^---$/m', '<div class="my-8 flex items-center"><div class="flex-1 border-t border-gray-300"></div><span class="px-4 text-gray-500 text-sm">• • •</span><div class="flex-1 border-t border-gray-300"></div></div>', $html);
                
                // Add info boxes for special content
                $html = preg_replace('/\*\*Lưu ý:\*\*(.+?)(?=\n\n|\n#|\n##|\Z)/s', '<div class="info-box bg-blue-50 border-l-4 border-blue-400 p-4 my-6 rounded-r-lg"><div class="flex"><div class="flex-shrink-0"><span class="text-blue-400">💡</span></div><div class="ml-3"><p class="text-sm text-blue-700"><strong>Lưu ý:</strong>$1</p></div></div></div>', $html);
                
                $html = preg_replace('/\*\*Quan trọng:\*\*(.+?)(?=\n\n|\n#|\n##|\Z)/s', '<div class="info-box bg-red-50 border-l-4 border-red-400 p-4 my-6 rounded-r-lg"><div class="flex"><div class="flex-shrink-0"><span class="text-red-400">⚠️</span></div><div class="ml-3"><p class="text-sm text-red-700"><strong>Quan trọng:</strong>$1</p></div></div></div>', $html);
                
                $html = preg_replace('/\*\*Thành công:\*\*(.+?)(?=\n\n|\n#|\n##|\Z)/s', '<div class="info-box bg-green-50 border-l-4 border-green-400 p-4 my-6 rounded-r-lg"><div class="flex"><div class="flex-shrink-0"><span class="text-green-400">✅</span></div><div class="ml-3"><p class="text-sm text-green-700"><strong>Thành công:</strong>$1</p></div></div></div>', $html);
                
                // Line breaks
                $html = nl2br($html);
                
                // Clean up empty paragraphs
                $html = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/', '<br>', $html);
                
                echo $html;
            @endphp
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="mt-8 text-center">
        <div class="flex justify-center space-x-4">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Quay lại trang chủ
            </a>
            <button onclick="window.print()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                In tài liệu
            </button>
        </div>
    </div>

</div>

<style>
/* Enhanced prose styling */
.prose h1:first-child {
    margin-top: 0;
}

.prose h1, .prose h2, .prose h3, .prose h4 {
    transition: all 0.3s ease;
}

.prose h1:hover, .prose h2:hover, .prose h3:hover, .prose h4:hover {
    transform: translateX(5px);
}

.prose ul, .prose ol {
    margin: 1rem 0;
    padding-left: 0;
}

.prose li {
    margin: 0.5rem 0;
    line-height: 1.7;
    padding: 0.25rem 0;
}

.prose pre {
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.prose code {
    font-family: 'Fira Code', 'Monaco', 'Consolas', monospace;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.prose code:hover {
    background-color: #f9fafb;
    transform: scale(1.02);
}

.prose blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6b7280;
    background-color: #f8fafc;
    border-radius: 0 0.5rem 0.5rem 0;
    padding: 1rem;
}

/* Code block animations */
.code-block {
    transition: all 0.3s ease;
}

.code-block:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

/* Copy button animation */
.copy-btn {
    transition: all 0.2s ease;
}

.copy-btn:hover {
    transform: scale(1.1);
}

.copy-btn.copied {
    background-color: #10b981;
    color: white;
}

/* Info boxes animations */
.info-box {
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Smooth scrolling for headers */
.prose h1, .prose h2, .prose h3, .prose h4 {
    scroll-margin-top: 2rem;
}

/* Enhanced link styling */
.prose a {
    position: relative;
    text-decoration: none;
}

.prose a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: #3b82f6;
    transition: width 0.3s ease;
}

.prose a:hover::after {
    width: 100%;
}
</style>

<script>
function copyToClipboard(button) {
    const codeBlock = button.closest('.code-block') || button.closest('div').querySelector('pre code');
    const text = codeBlock.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        // Visual feedback
        const originalText = button.textContent;
        button.textContent = '✅ Copied!';
        button.classList.add('copied');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        button.textContent = '❌ Failed';
        setTimeout(() => {
            button.textContent = '📋 Copy';
        }, 2000);
    });
}

// Add smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add copy functionality to all code blocks
    const codeBlocks = document.querySelectorAll('pre code');
    codeBlocks.forEach(block => {
        const parent = block.closest('div');
        if (parent && !parent.querySelector('.copy-btn')) {
            const copyBtn = document.createElement('button');
            copyBtn.className = 'copy-btn absolute top-2 right-2 text-xs text-gray-500 hover:text-gray-700 cursor-pointer bg-white px-2 py-1 rounded border';
            copyBtn.textContent = '📋 Copy';
            copyBtn.onclick = () => copyToClipboard(copyBtn);
            
            if (parent.style.position !== 'relative') {
                parent.style.position = 'relative';
            }
            parent.appendChild(copyBtn);
        }
    });
});
</script>
@endsection
