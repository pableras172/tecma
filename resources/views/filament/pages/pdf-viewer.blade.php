<div>
    @if(isset($fileUrl))
        <div style="width: 100%; height: 80vh;">
            <iframe 
                src="{{ $fileUrl }}" 
                style="width: 100%; height: 100%; border: none;"
                type="application/pdf"
            ></iframe>
        </div>
    @else
        <p>No se pudo cargar el documento.</p>
    @endif
</div>
