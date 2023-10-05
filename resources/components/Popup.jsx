import React from 'react';

const Popup = ({ feature, position }) => {
    if (!feature) return null; // Don't render anything if there's no feature

    return (
        <div style={{ position: 'absolute', top: position.y, left: position.x, background: 'white', padding: '10px', borderRadius: '5px', zIndex: 50}}>
            <h3>Feature Data:</h3>
            <pre>{JSON.stringify(feature.properties, null, 2)}</pre>
        </div>
    );
};

export default Popup;
