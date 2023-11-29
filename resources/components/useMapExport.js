import { useCallback, useRef } from 'react';
import FileSaver from "file-saver";
import { toast } from 'react-toastify';

const FILENAME = "map.png"

const useMapExport = () => {
    const mapContainerRef = useRef();

    const showErrorToast = () => {
        console.error("Unable to export map.");
        toast.error('Es gab einen Fehler beim Exportieren der Karte.');
    }

    const handleExport = useCallback(() => {
        if (!mapContainerRef.current) {
            showErrorToast();
            return;
        }

        requestAnimationFrame(() => {
            try {
                const mapGL = mapContainerRef.current.getMapRef().getMap();
                const maplibreCanvas = mapGL.getCanvas();

                if (!maplibreCanvas || !(maplibreCanvas instanceof HTMLCanvasElement)) {
                    showErrorToast();
                    return;
                }

                maplibreCanvas.toBlob((blob) => {
                    if (!blob) {
                        showErrorToast();
                        return;
                    }
                    FileSaver.saveAs(blob, FILENAME);
                    toast.success('Kartenexport erfolgreich!');
                });

            } catch (error) {
                showErrorToast();
            }
        });
    }, []);

    return { handleExport, mapContainerRef };
}

export default useMapExport;
