import React, { useEffect, useState } from "react";

const pageSizes = {
    A2: { portrait: { width: 420, height: 594 }, landscape: { width: 594, height: 420 } },
    A3: { portrait: { width: 297, height: 420 }, landscape: { width: 420, height: 297 } },
    A4: { portrait: { width: 210, height: 297 }, landscape: { width: 297, height: 210 } },
    A5: { portrait: { width: 148, height: 210 }, landscape: { width: 210, height: 148 } },
};

const PreviewOverlay = ({ exportOptions, setExportOptions, mapDimensions }) => {
    const [previewDimensions, setPreviewDimensions] = useState({width: 0, height: 0});

    useEffect(() => {
        const { pageSize, orientation } = exportOptions;
        if (pageSize && orientation && pageSizes[pageSize]) {
            const newDimensions = pageSizes[pageSize][orientation.toLowerCase()];
            setPreviewDimensions(newDimensions);

            if (mapDimensions) {
                const x = (mapDimensions.width - newDimensions.width) / 2;
                const y = (mapDimensions.height - newDimensions.height) / 2;

                setExportOptions({ ...exportOptions, x, y, ...newDimensions });
            }
        }
    }, [exportOptions]);

    const previewStyle = {
        position: "absolute",
        border: "2px dashed red",
        width: `${previewDimensions.width}px`, // Update these values to match the scale of your map
        height: `${previewDimensions.height}px`, // For example, you might need to multiply by a scaling factor
        transform: "translate(-50%, -50%)", // Center the overlay. Adjust as necessary.
        top: "50%", // Adjust these values to position the overlay
        left: "50%", // where you want on the map
        zIndex: "50"
    };

    return (
        <div style={previewStyle}>
        </div>
    );
};

export default PreviewOverlay;
