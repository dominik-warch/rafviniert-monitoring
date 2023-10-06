import React from 'react';
import { popupMapping} from "./PopupTemplates.jsx";

const Popup = ({ featureEvent, position }) => {
    if (!featureEvent.object ) return null; // Don't render anything if there's no feature

    const PopupTemplate = popupMapping[featureEvent.layer.id];
    if (!PopupTemplate) {
        return (
            <div style={{ position: 'absolute', top: position.y, left: position.x, background: 'white', padding: '10px', borderRadius: '5px', zIndex: 50}}>
                <p>Template not found for layer {featureEvent.layer.id}</p>
                <pre>{JSON.stringify(featureEvent.object.properties, null, 2)}</pre>
            </div>
        );
    };

    return (
        <div style={{ position: 'absolute', top: position.y, left: position.x, background: 'white', padding: '10px', borderRadius: '5px', zIndex: 50}}>
            <PopupTemplate feature={featureEvent.object} />
        </div>
    );
};

export default Popup;
